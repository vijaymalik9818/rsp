/*
Template Name: Velzon - Admin & Dashboard Template
Author: Themesbrand
Website: https://Themesbrand.com/
Contact: Themesbrand@gmail.com
File: seller-details init js
*/

// table-product-list-all 
var TableProductListAll = document.getElementById('table-product-list-all');
if (TableProductListAll) {
    new gridjs.Grid({
        columns: [
            {
				name: 'MLS#',
				width: '70px',
				sort: {
					enabled: false
				},
				data: (function (row) {
					return gridjs.html('<div class="form-check checkbox-product-list">\
					<input class="form-check-input" type="checkbox" value="'+ row.id + '" id="checkbox-' + row.id + '">\
					<label class="form-check-label" for="checkbox-'+ row.id + '"></label>\
				  </div>');
				})
			},
            {
                name: 'Address',
                width: '110px',
                formatter: (function (cell) {
                    return gridjs.html('<div class="d-flex align-items-center">' +
                        '<div class="flex-shrink-0 me-3">' +
                        '<div class="avatar-sm bg-light rounded p-1"><img src="assets/images/products/' + cell[0] + '" alt="" class="img-fluid d-block"></div>' +
                        '</div>' +
                        '<div class="flex-grow-1">' +
                        '<h5 class="fs-14 mb-1"><a href="apps-ecommerce-product-details.html" class="text-body">' + cell[1] + '</a></h5>' +
                        '<p class="text-muted mb-0">Category : <span class="fw-medium">' + cell[2] + '</span></p>' +
                        '</div>' +
                        '</div>');
                })
            },

            {
                name: 'City',
                width: '94px',
            },
            {
                name: 'Zipcode',
                width: '60px',
            },
            {
                name: 'Price',
                width: '80px',
            },
            {
                name: 'Property Table',
                width: '70px',
                formatter: (function (cell) {
                    return gridjs.html('<span class="badge bg-light text-body fs-12 fw-medium"><i class="mdi mdi-star text-warning me-1"></i>' + cell + '</span></td>');
                })
            },
            {
                name: 'Subtype',
                width: '80px',
                formatter: (function (cell) {
                    return gridjs.html(cell[0] + '<small class="text-muted ms-1">' + cell[1] + '</small>');
                })
            },
            {
                name: 'Status',
                width: '80px',
                formatter: (function (cell) {
                    return gridjs.html(cell[0] + '<small class="text-muted ms-1">' + cell[1] + '</small>');
                })
            },
            {
                name: 'Diamond',
                width: '90px',
                formatter: (function (cell) {
                    return gridjs.html(cell[0] + '<small class="text-muted ms-1">' + cell[1] + '</small>');
                })
            },
            {
                name: "Action",
                width: '80px',
                sort: {
                    enabled: false
                },
                formatter: (function (cell) {
                    return gridjs.html('<div class="dropdown">' +
                        '<button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">' +
                        '<i class="ri-more-fill"></i>' +
                        '</button>' +
                        '<ul class="dropdown-menu dropdown-menu-end">' +
                        '<li><a class="dropdown-item" href="apps-ecommerce-product-details.html"><i class="ri-eye-fill align-bottom me-2 text-muted"></i> View</a></li>' +
                        '<li><a class="dropdown-item" href="apps-ecommerce-add-product.html"><i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit</a></li>' +
                        '<li class="dropdown-divider"></li>' +
                        '<li><a class="dropdown-item" href="#!"><i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i> Delete</a></li>' +
                        '</ul>' +
                        '</div>');
                })
            }
        ],
        className: {
            th: 'text-muted',
        },
        pagination: {
            limit: 10
        },
        sort: true,
        data: [
            [
                ["img-1.png", "Half Sleeve Round Neck T-Shirts", "Clothes"], "12", "$ 115.00", "48", "4.2", ["12 Oct, 2021", "10:05 AM"]
            ],
          
        ]
    }).render(document.getElementById("table-product-list-all"));
}



// get colors array from the string
function getChartColorsArray(chartId) {
    if (document.getElementById(chartId) !== null) {
        var colors = document.getElementById(chartId).getAttribute("data-colors");
        if (colors) {
            colors = JSON.parse(colors);
            return colors.map(function (value) {
                var newValue = value.replace(" ", "");
                if (newValue.indexOf(",") === -1) {
                    var color = getComputedStyle(document.documentElement).getPropertyValue(
                        newValue
                    );
                    if (color) return color;
                    else return newValue;
                } else {
                    var val = value.split(",");
                    if (val.length == 2) {
                        var rgbaColor = getComputedStyle(
                            document.documentElement
                        ).getPropertyValue(val[0]);
                        rgbaColor = "rgba(" + rgbaColor + "," + val[1] + ")";
                        return rgbaColor;
                    } else {
                        return newValue;
                    }
                }
            });
        }
    }
}

//Revenue Chart
var linechartcustomerColors = getChartColorsArray("customer_impression_charts");
if (linechartcustomerColors) {
    var options = {
        series: [{
                name: "Orders",
                type: "area",
                data: [34, 65, 46, 68, 49, 61, 42, 44, 78, 52, 63, 67],
            },
            {
                name: "Earnings",
                type: "bar",
                data: [
                    89.25, 98.58, 68.74, 108.87, 77.54, 84.03, 51.24, 28.57, 92.57, 42.36,
                    88.51, 36.57,
                ],
            },
            {
                name: "Refunds",
                type: "line",
                data: [8, 12, 7, 17, 21, 11, 5, 9, 7, 29, 12, 35],
            },
        ],
        chart: {
            height: 370,
            type: "line",
            toolbar: {
                show: false,
            },
        },
        stroke: {
            curve: "straight",
            dashArray: [0, 0, 8],
            width: [2, 0, 2.2],
        },
        fill: {
            opacity: [0.1, 0.9, 1],
        },
        markers: {
            size: [0, 0, 0],
            strokeWidth: 2,
            hover: {
                size: 4,
            },
        },
        xaxis: {
            categories: [
                "Jan",
                "Feb",
                "Mar",
                "Apr",
                "May",
                "Jun",
                "Jul",
                "Aug",
                "Sep",
                "Oct",
                "Nov",
                "Dec",
            ],
            axisTicks: {
                show: false,
            },
            axisBorder: {
                show: false,
            },
        },
        grid: {
            show: true,
            xaxis: {
                lines: {
                    show: true,
                },
            },
            yaxis: {
                lines: {
                    show: false,
                },
            },
            padding: {
                top: 0,
                right: -2,
                bottom: 15,
                left: 10,
            },
        },
        legend: {
            show: true,
            horizontalAlign: "center",
            offsetX: 0,
            offsetY: -5,
            markers: {
                width: 9,
                height: 9,
                radius: 6,
            },
            itemMargin: {
                horizontal: 10,
                vertical: 0,
            },
        },
        plotOptions: {
            bar: {
                columnWidth: "30%",
                barHeight: "70%",
            },
        },
        colors: linechartcustomerColors,
        tooltip: {
            shared: true,
            y: [{
                    formatter: function (y) {
                        if (typeof y !== "undefined") {
                            return y.toFixed(0);
                        }
                        return y;
                    },
                },
                {
                    formatter: function (y) {
                        if (typeof y !== "undefined") {
                            return "$" + y.toFixed(2) + "k";
                        }
                        return y;
                    },
                },
                {
                    formatter: function (y) {
                        if (typeof y !== "undefined") {
                            return y.toFixed(0) + " Sales";
                        }
                        return y;
                    },
                },
            ],
        },
    };
    var chart = new ApexCharts(
        document.querySelector("#customer_impression_charts"),
        options
    );
    chart.render();
}

var counterValue = document.querySelector('.counter-value');
if (counterValue) {

    (counter = document.querySelectorAll(".counter-value")),
    (speed = 250);
    counter &&
        Array.from(counter).forEach(function (a) {
            !(function e() {
                var t = +a.getAttribute("data-target"),
                    n = +a.innerText,
                    o = t / speed;
                o < 1 && (o = 1),
                    n < t ?
                    ((a.innerText = (n + o).toFixed(0)), setTimeout(e, 1)) :
                    (a.innerText = t);
            })();
        });
}

// Vertical Swiper
var VerticalSwiper = document.querySelector('.vertical-swiper');
if (VerticalSwiper) {
    var swiper = new Swiper(".vertical-swiper", {
        slidesPerView: 2,
        spaceBetween: 10,
        mousewheel: true,
        loop: true,
        direction: "vertical",
        autoplay: {
            delay: 2500,
            disableOnInteraction: false,
        },
    });
}