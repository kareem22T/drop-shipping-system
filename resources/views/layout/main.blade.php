<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="/assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="/assets/img/favicon.png">
  <title>
    @yield('title')
  </title>
  <!--     Fonts and icons     -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
  <!-- Nucleo Icons -->
  <link href="/assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="/assets/css/nucleo-svg.css" rel="stylesheet" />
  <!-- Font Awesome Icons -->
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <link href="/assets/css/nucleo-svg.css" rel="stylesheet" />
  <!-- CSS Files -->
  <link id="pagestyle" href="/assets/css/soft-ui-dashboard.css?v=1.0.7" rel="stylesheet" />
  {{-- <script src="/build/assets/app-CS7l-VsX.js"></script> --}}
  <style>
    .page-item.active .page-link {
        color: #ffffff !important
    }
  </style>
      <script>
        document.addEventListener('DOMContentLoaded', function() {
            let lastChecked = new Date().toISOString();

            function checkForWarnings() {
                fetch(`/check-warnings?lastChecked=${lastChecked}`)
                    .then(response => response.json())
                    .then(warnings => {
                        if (warnings.length > 0) {
                            showPopup(warnings);
                            lastChecked = new Date().toISOString();
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }
            function generateWarningMessage(warning) {
                if (warning.change === "exp_warn") {
                    let content = "Product <b>" + warning.product.name + "</b> " + " discount is about to expired ";
                    playSound2()
                    setTimeout(() => {
                        playSound2()
                    }, 600);
                    setTimeout(() => {
                        playSound2()
                    }, 1200);
                    return content ;
                }
                else {
                    let content = "Product <b>" + warning.product.name + "</b> " + warning.change + " has changed from ";
                    if (warning.change === "discount_value") {
                        content = "Product <b>" + warning.product.name + "</b> " + "Discount Value" + " has changed from ";
                    }
                    content += "<b>";
                    if (warning.change === "stock") {
                        content += warning.old == 1 ? "In Stock" : (warning.old == 2 ? "Managed Stock" : "Out Of Stock");
                    } else {
                        content += warning.old;
                    }
                    content += "</b>";
                    content += " to ";
                    content += "<b>";
                    if (warning.change === "stock") {
                        content += warning.new == 1 ? "In Stock" : (warning.new == 2 ? "Managed Stock" : "Out Of Stock");
                    } else {
                        content += warning.new;
                    }
                    content += "</b>";
                    if (warning.change === "stock") {
                        playSound()
                        setTimeout(() => {
                            playSound()
                        }, 600);
                        setTimeout(() => {
                            playSound()
                        }, 1200);
                    } else if (warning.change === "price" || warning.change === "discount_value") {
                        playSound3()
                        setTimeout(() => {
                            playSound3()
                        }, 600);
                    }
                    return content ;
                }

            }

            function showPopup(warnings) {
                let messages = warnings.map(warning => `<p style="padding: 8px;background: #80808029;font-size: 14px;">${generateWarningMessage(warning)}<a href="" class="text-danger bold remove_warning" style="padding: 0 12px;font-weight: bold;" warning_id="${warning.id}">Remove</a></p>`).join('');
                document.getElementById('warningMessages').innerHTML += messages;
                $('#warningModal').modal('show');
            }

            function playSound() {
                let audio = new Audio('/beep-warning-6387.mp3');
                audio.play().catch(error => console.error('Error playing sound:', error));
            }

            function playSound2() {
                let audio1 = new Audio('/warning-sound-6686.mp3');
                audio1.play().catch(error => console.error('Error playing sound:', error));
            }

            function playSound3() {
                let audio2 = new Audio('/severe-warning-alarm-98704.mp3');
                audio2.play().catch(error => console.error('Error playing sound:', error));
            }

            setInterval(checkForWarnings, 3000 * 10); // Check every 5 minutes
        });
    </script>
</head>

<body class="g-sidenav-show  bg-gray-100">
    <div class="modal fade" id="warningModal" tabindex="-1" role="dialog" aria-labelledby="warningModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="  min-width: calc(100vw - 32px);">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="warningModalLabel">New Warnings</h5>
                <button type="button" class="closeNew" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="warningMessages">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary closeNew" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
  <aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3 " id="sidenav-main">
    <div class="sidenav-header">
      <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
      <a class="navbar-brand m-0" href=" https://demos.creative-tim.com/soft-ui-dashboard/pages/dashboard.html " target="_blank">
        <img src="/assets/img/logo-ct-dark.png" class="navbar-brand-img h-100" alt="main_logo">
        <span class="ms-1 font-weight-bold">Drop Shipping Dashboard</span>
      </a>
    </div>
    <hr class="horizontal dark mt-0">
    <div class="collapse navbar-collapse  w-auto " id="sidenav-collapse-main">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link  active" href="../pages/dashboard.html">
            <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
              <svg width="12px" height="12px" viewBox="0 0 45 40" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                <title>shop </title>
                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                  <g transform="translate(-1716.000000, -439.000000)" fill="#FFFFFF" fill-rule="nonzero">
                    <g transform="translate(1716.000000, 291.000000)">
                      <g transform="translate(0.000000, 148.000000)">
                        <path class="color-background opacity-6" d="M46.7199583,10.7414583 L40.8449583,0.949791667 C40.4909749,0.360605034 39.8540131,0 39.1666667,0 L7.83333333,0 C7.1459869,0 6.50902508,0.360605034 6.15504167,0.949791667 L0.280041667,10.7414583 C0.0969176761,11.0460037 -1.23209662e-05,11.3946378 -1.23209662e-05,11.75 C-0.00758042603,16.0663731 3.48367543,19.5725301 7.80004167,19.5833333 L7.81570833,19.5833333 C9.75003686,19.5882688 11.6168794,18.8726691 13.0522917,17.5760417 C16.0171492,20.2556967 20.5292675,20.2556967 23.494125,17.5760417 C26.4604562,20.2616016 30.9794188,20.2616016 33.94575,17.5760417 C36.2421905,19.6477597 39.5441143,20.1708521 42.3684437,18.9103691 C45.1927731,17.649886 47.0084685,14.8428276 47.0000295,11.75 C47.0000295,11.3946378 46.9030823,11.0460037 46.7199583,10.7414583 Z"></path>
                        <path class="color-background" d="M39.198,22.4912623 C37.3776246,22.4928106 35.5817531,22.0149171 33.951625,21.0951667 L33.92225,21.1107282 C31.1430221,22.6838032 27.9255001,22.9318916 24.9844167,21.7998837 C24.4750389,21.605469 23.9777983,21.3722567 23.4960833,21.1018359 L23.4745417,21.1129513 C20.6961809,22.6871153 17.4786145,22.9344611 14.5386667,21.7998837 C14.029926,21.6054643 13.533337,21.3722507 13.0522917,21.1018359 C11.4250962,22.0190609 9.63246555,22.4947009 7.81570833,22.4912623 C7.16510551,22.4842162 6.51607673,22.4173045 5.875,22.2911849 L5.875,44.7220845 C5.875,45.9498589 6.7517757,46.9451667 7.83333333,46.9451667 L19.5833333,46.9451667 L19.5833333,33.6066734 L27.4166667,33.6066734 L27.4166667,46.9451667 L39.1666667,46.9451667 C40.2482243,46.9451667 41.125,45.9498589 41.125,44.7220845 L41.125,22.2822926 C40.4887822,22.4116582 39.8442868,22.4815492 39.198,22.4912623 Z"></path>
                      </g>
                    </g>
                  </g>
                </g>
              </svg>
            </div>
            <span class="nav-link-text ms-1">Dashboard</span>
          </a>
        </li>
      </ul>
    </div>
  </aside>
  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
    <!-- Navbar -->
    <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
      <div class="container-fluid py-1 px-3">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
            <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Pages</a></li>
            <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Dashboard</li>
          </ol>
          <h6 class="font-weight-bolder mb-0">Dashboard</h6>
        </nav>
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
          <div class="ms-md-auto pe-md-3 d-flex align-items-center">
            <form action="{{ request()->fullUrlWithQuery([]) }}" method="GET" class="d-flex" style="gap: 8px; align-items: end">
                <div class="input-group">
                    <span class="input-group-text text-body"><i class="fas fa-search" aria-hidden="true"></i></span>
                    <input type="text" class="form-control" name="search" placeholder="Type here...">
                </div>
                <button type="submit" class="btn btn-primary m-0">Search</button>
                @if(request()->search)
                    <a href="{{request()->fullUrlWithQuery(['search' => ''])}}"  class="btn btn-danger m-0">Cancel</a>
                @endif
            </form>

        </div>
        </div>
      </div>
    </nav>
    <!-- End Navbar -->
    <div class="container-fluid py-4">
        @yield("content")
        <footer class="footer pt-3  ">
          <div class="container-fluid">
            <div class="row align-items-center justify-content-lg-between">
              <div class="col-lg-6 mb-lg-0 mb-4">
                <div class="copyright text-center text-sm text-muted text-lg-start">
                  © <script>
                    document.write(new Date().getFullYear())
                  </script>,
                  powerd <i class="fa fa-heart"></i> by
                  <a href="https://webbing-agency.com/" class="font-weight-bold" target="_blank">Webbing Agency</a>
                </div>
              </div>
              </div>
            </div>
          </div>
        </footer>
      </div>
  </main>
  <!--   Core JS Files   -->
  <script src="/assets/js/core/popper.min.js"></script>
  <script src="/assets/js/vue.js"></script>
  <script src="/assets/js/core/bootstrap.min.js"></script>
  <script src="/assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="/assets/js/plugins/smooth-scrollbar.min.js"></script>
  <script src="/assets/js/plugins/chartjs.min.js"></script>
  <script>
    var ctx = document.getElementById("chart-bars").getContext("2d");

    new Chart(ctx, {
      type: "bar",
      data: {
        labels: ["Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
        datasets: [{
          label: "Sales",
          tension: 0.4,
          borderWidth: 0,
          borderRadius: 4,
          borderSkipped: false,
          backgroundColor: "#fff",
          data: [450, 200, 100, 220, 500, 100, 400, 230, 500],
          maxBarThickness: 6
        }, ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false,
          }
        },
        interaction: {
          intersect: false,
          mode: 'index',
        },
        scales: {
          y: {
            grid: {
              drawBorder: false,
              display: false,
              drawOnChartArea: false,
              drawTicks: false,
            },
            ticks: {
              suggestedMin: 0,
              suggestedMax: 500,
              beginAtZero: true,
              padding: 15,
              font: {
                size: 14,
                family: "Open Sans",
                style: 'normal',
                lineHeight: 2
              },
              color: "#fff"
            },
          },
          x: {
            grid: {
              drawBorder: false,
              display: false,
              drawOnChartArea: false,
              drawTicks: false
            },
            ticks: {
              display: false
            },
          },
        },
      },
    });


    var ctx2 = document.getElementById("chart-line").getContext("2d");

    var gradientStroke1 = ctx2.createLinearGradient(0, 230, 0, 50);

    gradientStroke1.addColorStop(1, 'rgba(203,12,159,0.2)');
    gradientStroke1.addColorStop(0.2, 'rgba(72,72,176,0.0)');
    gradientStroke1.addColorStop(0, 'rgba(203,12,159,0)'); //purple colors

    var gradientStroke2 = ctx2.createLinearGradient(0, 230, 0, 50);

    gradientStroke2.addColorStop(1, 'rgba(20,23,39,0.2)');
    gradientStroke2.addColorStop(0.2, 'rgba(72,72,176,0.0)');
    gradientStroke2.addColorStop(0, 'rgba(20,23,39,0)'); //purple colors

    new Chart(ctx2, {
      type: "line",
      data: {
        labels: ["Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
        datasets: [{
            label: "Mobile apps",
            tension: 0.4,
            borderWidth: 0,
            pointRadius: 0,
            borderColor: "#cb0c9f",
            borderWidth: 3,
            backgroundColor: gradientStroke1,
            fill: true,
            data: [50, 40, 300, 220, 500, 250, 400, 230, 500],
            maxBarThickness: 6

          },
          {
            label: "Websites",
            tension: 0.4,
            borderWidth: 0,
            pointRadius: 0,
            borderColor: "#3A416F",
            borderWidth: 3,
            backgroundColor: gradientStroke2,
            fill: true,
            data: [30, 90, 40, 140, 290, 290, 340, 230, 400],
            maxBarThickness: 6
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false,
          }
        },
        interaction: {
          intersect: false,
          mode: 'index',
        },
        scales: {
          y: {
            grid: {
              drawBorder: false,
              display: true,
              drawOnChartArea: true,
              drawTicks: false,
              borderDash: [5, 5]
            },
            ticks: {
              display: true,
              padding: 10,
              color: '#b2b9bf',
              font: {
                size: 11,
                family: "Open Sans",
                style: 'normal',
                lineHeight: 2
              },
            }
          },
          x: {
            grid: {
              drawBorder: false,
              display: false,
              drawOnChartArea: false,
              drawTicks: false,
              borderDash: [5, 5]
            },
            ticks: {
              display: true,
              color: '#b2b9bf',
              padding: 20,
              font: {
                size: 11,
                family: "Open Sans",
                style: 'normal',
                lineHeight: 2
              },
            }
          },
        },
      },
    });
  </script>
  <script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = {
        damping: '0.5'
      }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
  </script>
  <!-- Github buttons -->
  <script async defer src="https://buttons.github.io/buttons.js"></script>
  <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="/assets/js/soft-ui-dashboard.min.js?v=1.0.7"></script>
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <script>
    $(document).on("click", ".remove_warning", function(e) {
        e.preventDefault()
        if ( confirm("Are Your sure to remove warning") )
        fetch(`/delete-warning?id=${$(this).attr("warning_id")}`)
            .then(response => response.json())
            .then(res => {
                if (res.status == true) {
                    $(this).parent().remove()
                }
            })
            .catch(error => console.error('Error:', error));

    })
  </script>
    <script>
        function generateWarningMessage(warning) {
            let content = "Product <b>" + warning.product.name + "</b> " + warning.change + " has changed from ";
            content += "<b>";
            if (warning.change === "stock") {
                content += warning.old == 1 ? "In Stock" : (warning.old == 2 ? "Managed Stock" : "Out Of Stock");
            } else {
                content += warning.old;
            }
            content += "</b>";
            content += " to ";
            content += "<b>";
            if (warning.change === "stock") {
                content += warning.new == 1 ? "In Stock" : (warning.new == 2 ? "Managed Stock" : "Out Of Stock");
            } else {
                content += warning.new;
            }
            content += "</b>";
            return content;
        }

        function showPopup(warnings) {
            let messages = warnings.map(warning => `
                <p style="padding: 8px;background: #80808029;font-size: 14px;">
                    ${generateWarningMessage(warning)}
                    <a href="" class="text-danger bold remove_warning" style="padding: 0 12px;font-weight: bold;" data-warning-id="${warning.id}">Remove</a>
                </p>
            `).join('');
            document.getElementById('warningMessages').innerHTML += messages;
            $('#warningModal').modal('show');
        }

        $(document).ready(function() {
            // Open the modal on page load if there are warnings
            @if(count($warnings) > 0)
                $('#warningModalOld').modal('show');
            @endif

            // Remove all warnings
            $('#removeAllWarningsButton').click(function() {
                // Make an AJAX request to remove all warnings
                if ( confirm("Are Your sure to remove all warnings") )
                    fetch(`/delete-all-warnings`)
                        .then(response => response.json())
                        .then(res => {
                            if (res.status == true) {
                                window.location.reload()
                            }
                        })
                        .catch(error => console.error('Error:', error));
            });
            $('.remove-product').click(function() {
                if ( confirm("Are Your sure to remove this product") )
                    window.location.href = `/remove-product?id=${$(this).attr("data-product-id")}`
            });
        });
        $(".closeOld").on("click", function () {
            $('#warningModalOld').modal('hide');
        })
        $(".closeNew").on("click", function () {
            $('#warningModal').modal('hide');
        })
    </script>
    @yield('scripts')
</body>

</html>
