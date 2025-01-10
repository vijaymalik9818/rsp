<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    </head>
    <style>
      #contacIcon::after {
        content: "Contact Us";
        display: none;
        position: absolute;
        font-size: 13px;
        top: 85%;
        height: 32px;
        width: 69px;
        left: 50%;
        transform: translateX(-50%);
        background-color: black;
        color: #fff;
        padding: 0px 1px;
        border-radius: 10px;
        white-space: nowrap;
        z-index: 1000;
    }
    
    
    #contacIcon:hover::after {
        display: block;
    }
    
    #joinrep::after {
        content: "Join Rep";
        display: none;
        position: absolute;
        font-size: 13px;
        top: 85%;
        height: 32px;
        width: 69px;
        left: 50%;
        transform: translateX(-50%);
        background-color: black;
        color: #fff;
        padding: 0px 1px;
        border-radius: 10px;
        white-space: nowrap;
        z-index: 1000;
    }
    
    
    #joinrep:hover::after {
        display: block;
    }
    #ticket::after {
        content: "Join Rep";
        display: none;
        position: absolute;
        font-size: 13px;
        top: 85%;
        height: 32px;
        width: 69px;
        left: 50%;
        transform: translateX(-50%);
        background-color: black;
        color: #fff;
        padding: 0px 1px;
        border-radius: 10px;
        white-space: nowrap;
        z-index: 1000;
    }
    
    
    #ticket:hover::after {
        display: block;
    }
   #dashboard::after {
    content: "REALTORS®  \A  Staff  \A   Leads";
    display: none;
    position: absolute;
    font-size: 10px;
    top: 85%;
    height: 58px;
    width: 69px;
    left: 50%;
    transform: translateX(-50%);
    background-color: black;
    color: #fff;
    padding: 0px 1px;
    border-radius: 10px;
    white-space: pre;
    line-height: 16px;
    z-index: 1000;
    }
    
    
    #dashboard:hover::after {
        display: block;
    }
    #tour::after {
        content: "Property Reviews \A Tour";
        display: none;
        position: absolute;
        font-size: 8px;
        top: 85%;
        height: 32px;
        width: 69px;
        left: 50%;
        transform: translateX(-50%);
        background-color: black;
        color: #fff;
        padding: 0px 1px;
        border-radius: 10px;
        white-space: pre;
        line-height: 16px;
        z-index: 1000;
    }
    
    #tour:hover::after {
        display: block;
    }
    #addticket::after {
        content: "Ticket";
        display: none;
        position: absolute;
        font-size: 13px;
        top: 85%;
        height: 32px;
        width: 69px;
        left: 50%;
        transform: translateX(-50%);
        background-color: black;
        color: #fff;
        padding: 0px 1px;
        border-radius: 10px;
        white-space: nowrap;
        z-index: 1000;
    }
    
    #addticket:hover::after {
        display: block;
    }
    #city::after {
        content: "City";
        display: none;
        position: absolute;
        font-size: 13px;
        top: 85%;
        height: 32px;
        width: 69px;
        left: 50%;
        transform: translateX(-50%);
        background-color: black;
        color: #fff;
        padding: 0px 1px;
        border-radius: 10px;
        white-space: nowrap;
        z-index: 1000;
    }
    
    
    #city:hover::after {
        display: block;
    }

    </style>
    
    <?php
    // $agent = auth::user;
    $role = Auth::user()->role;
    $id = Auth::user()->id;
    if ($role == 1) {
        $route = route('show-agents');
    } else {
        $route = route('agent.details', ['encodedString' => base64_encode($id)]);
    }
    ?>
    
    
    
             <div class="app-menu navbar-menu">
               <div class="navbar-brand-box">
    
                        <a href=<?php echo $route; ?> class="logo logo-dark">
                            <span class="logo-sm">
                                <img src="/assets/images/logo-sm.png" alt="" height="22">
                            </span>
                            <span class="logo-lg">
                                <img src="/assets/images/rsp.png" alt="" height="70">
                            </span>
                        </a>
    
                 <a href="javascript:void(0);" class="logo logo-light">
                      <span class="logo-sm">
                            <img src="/assets/images/logo-sm.png" alt="" height="22">
                         </span>
                            <span class="logo-lg">
                                <img src="/assets/images/logo-light.png" alt="" height="17">
                            </span>
                        </a>
                        <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover"
                            id="vertical-hover">
                            <i class="ri-record-circle-line"></i>
                        </button>
                    </div>
                  <div id="scrollbar">
                     <div class="container-fluid" >
                          <div id="two-column-menu">
                            </div>
                            <ul class="navbar-nav" id="navbar-nav">
                                <li class="menu-title"  ><span data-key="t-menu">Menu</span></li>
                                <li class="nav-item" >
                                    <?php if($role == 1){?>
                                    <a class="nav-link menu-link" href="#sidebarDashboards" data-bs-toggle="collapse" role="button"
                                        aria-expanded="false" aria-controls="sidebarDashboards" style="margin-top:20px;" id="dashboard">
                                        <i class="ri-user-3-fill"></i>
                                      <span data-key="t-dashboards">Dashboards</span>
                                    </a>
                                    <div class="collapse menu-dropdown" id="sidebarDashboards">
                                        <ul class="nav nav-sm flex-column">
                                            <li class="nav-item" >
                                                <a href="/agents" class="nav-link" data-key="t-analytics" style="margin-top:15px;" > REALTORS&#174; </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="/staff" class="nav-link" data-key="t-analytics"> Staff </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="/leads" class="nav-link" data-key="t-analytics"> Leads </a>
                                            </li>
                                            
                                        </ul>
                                    </div>
                                    <?php } else{?>
                                            <a class="nav-link menu-link" href="/ticket" id="addticket" role="button">
                                                <i class="ri-ticket-line"></i>
                                            </a>     
                                        <?php } ?>
                                            
                                   
                                    
                                </li>
    
                               
                                <li class="nav-item">
                                    <?php if($role == 1){?>
                                        <a href="/contactus" class="nav-link menu-link" data-key="t-horizontal" id="contacIcon" style="margin-top:20px;">
                                            <i class="ri-phone-fill"></i> <span data-key="t-layouts">Layouts</span> <span class="badge badge-pill bg-danger" data-key="t-hot">Hot</span>
                                        </a>
                                    <?php }?>
                                </li>
                                
                                
                                <li class="nav-item">                                    
                                    <?php if($role == 1){ ?>
                                        <a class="nav-link menu-link" href="/joinrep" id="joinrep" role="button">
                                            <i class="ri-user-add-fill"></i>
                                        </a>     
                                    <?php } ?>
                                        
                                </li>
                                
                                
    
    
                                <li class="nav-item">
                                    <?php if($role == 1){?>
                              <a class="nav-link menu-link" href="/allcity" role="button"
                                        id="city">
                                 <i class="bi bi-house-check-fill"></i>           
                                    </a>
                                    <?php }?>
                                </li>
                                
                               
    
    
                                <?php if($role == 1){ ?>
                            <li class="nav-item">      
                              <a class="nav-link menu-link" href="#sidebarLayouts2" data-bs-toggle="collapse" role="button"
                                  aria-expanded="false" aria-controls="sidebarLayouts2" id="tour">
                                  <i class="ri-building-4-fill"></i>
                                         </a>                 
                         
                        <div class="collapse menu-dropdown" id="sidebarLayouts2">
                        <ul class="nav nav-sm flex-column">             
                            <li class="nav-item">
                                            <a href="/propertyreviews" class="nav-link" data-key="t-hotizontal" style="margin-top:15px;">Property Reviews</a>
    
                                            
                                        </li>
                                <li>
                                    <a href="/tour" class="nav-link" data-key="t-hotizontal">Tour</a>
                                </li>
                                
                            </ul>
                        </div>
                    </li>
                    <?php }  ?>
    
                            </ul>
                    
                    
                    
                        </div>
    
                    </div>
                    
                </div>
    
    <div class="sidebar-background"></div>
    </div>
    
    <div class="vertical-overlay"></div>
    