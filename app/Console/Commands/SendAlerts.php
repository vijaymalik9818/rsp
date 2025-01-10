<?php

namespace App\Console\Commands;
use SendGrid;
// use SendGrid\Mail\Mail;
use App\Models\Properties;
use Illuminate\Console\Command;
use App\Models\SavedSearch;
use App\Models\User;
use Carbon\Carbon;

class SendAlerts extends Command
{
    protected $signature = 'alerts:send';
    protected $description = 'Send alerts to users based on saved searches';

    public function handle()
    {
        $this->info('Starting SendAlerts command...');

        \Log::info('SendAlerts command started...');

        $alerts = SavedSearch::all();
// dd($alerts);
        foreach ($alerts as $alert) {
            $duration = $alert->duration;
            $sentAt = $alert->sent_at;
// dd($sentAt);
            if ($duration && $sentAt) {
                $now = Carbon::now();
                // dd($now);

                switch ($duration) {
                    case 'daily':
                        if ($sentAt->diffInDays($now) >= 1) {
                    
                            $this->sendAlert($alert);
                        }
                        break;
                    case 'weekly':
                        if ($sentAt->diffInWeeks($now) >= 1) {
                            $this->sendAlert($alert);
                        }
                        break;
                    case 'monthly':
                        if ($sentAt->diffInMonths($now) >= 1) {
                            $this->sendAlert($alert);
                        }
                        case '5 month':
                            if ($sentAt->diffInMonths($now) >= 1) {
                                $this->sendAlert($alert);
                            }
                       
                        break;
                }
            }
        }

        \Log::info('SendAlerts command finished.');
        $this->info('SendAlerts command finished.');
    }

    private function sendAlert($alert)
    {
        $user = User::find($alert->user_id);
// dd($user);
        if ($user) {
            $query = Properties::where('updated_at', '>', $alert->sent_at);
            if ($alert->property_type) {
                $query->where('PropertyType', $alert->property_type);
            }
            
            if ($alert->min_price) {
                $query->where('ListPrice', '>=', $alert->min_price);
            }

            if ($alert->max_price) {
                $query->where('ListPrice', '<=', $alert->max_price);
            }

            if ($alert->beds) {
                $query->where('BedroomsTotal', '>=', $alert->beds);
            }

            if ($alert->bath) {
                $query->where('BathroomsFull', '>=', $alert->bath);
            }

            if ($alert->community) {
                $query->where('SubdivisionName', $alert->community);
            }

            if ($alert->min_yearbuilt) {
                $query->where('YearBuilt', '>=', $alert->min_yearbuilt);
            }

            if ($alert->max_yearbuilt) {
                $query->where('YearBuilt', '<=', $alert->max_yearbuilt);
            }

            if ($alert->min_sqft) {
                $query->where('LivingAreaSF', '>=', $alert->min_sqft);
            }

            if ($alert->max_sqft) {
                $query->where('LivingAreaSF', '<=', $alert->max_sqft);
            }

            if ($alert->min_acres) {
                $query->where('LotSizeAcres', '>=', $alert->min_acres);
            }

            if ($alert->max_acres) {
                $query->where('LotSizeAcres', '<=', $alert->max_acres);
            }
// dd($query);
            if ($alert->city) {
                $searchKey = $alert->city;
                $query->where(function ($query) use ($searchKey) {
                    $query->where('City', 'like', '%' . $searchKey . '%')
                        ->orWhere('ListingId', 'like', '%' . $searchKey . '%')
                        ->orWhereJsonContains('OtherColumns->PostalCode', $searchKey);
                });
            }
// dd($query);
            $query->orderBy('updated_at', 'desc')->take(10);
            $latestAdmin = User::where('role', 1)->latest()->first();
    
            $adminname = $latestAdmin->name;
            $adminemail = $latestAdmin->email;
            $properties = $query->get();
  
            $emailContent = view('emails.savedAlerts')->with([
                'properties' => $properties,
                'adminname' => $adminname,
                'adminemail' => $adminemail,
            ])->render();
        $email = new SendGrid\Mail\Mail();
        $email->setFrom(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
        $email->setSubject('New Listings Alert');
        $email->addTo($user->email, $user->name);
        $email->addContent("text/html", $emailContent);

        $sendgrid = new SendGrid(env('SENDGRID_API_KEY'));
        try {
            $response = $sendgrid->send($email);
            if ($response->statusCode() != 202) {
                \Log::error("Failed to send email to user: " . $response->body());
            } else {
                \Log::info('Alert sent to user: ' . $user->email);
                $this->info('Alert sent to user: ' . $user->email);
            }
        } catch (\Exception $e) {
            \Log::error("Failed to send email: " . $e->getMessage());
        }

        $alert->sent_at = Carbon::now();
        $alert->save();
    }
}

}