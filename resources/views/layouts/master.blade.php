<!DOCTYPE html>
<html lang="en" dir="rtl">

<body>

    @include('layouts.head')
    <!-- Main navbar -->
    <div class="navbar navbar-inverse">
        @include('layouts.nav')
        @include('layouts.nav-collapse')
    </div>
    <!-- /main navbar -->


    <!-- Page container -->
    <div class="page-container">

        <!-- Page content -->
        <div class="page-content">

            <!-- Main sidebar -->
                @include('layouts.sidebar')
            <!-- /main sidebar -->


            <!-- Main content -->
            <div class="content-wrapper">

                <!-- Page header -->
                @include('layouts.header')
                <!-- /page header -->


                <!-- Content area -->
                <div class="content">



                    <!-- Dashboard content -->
                    <div class="row">
                        @yield('content')
                    </div>
                    <!-- /dashboard content -->


                    <!-- Footer -->
                        @include('layouts.footer')
                    <!-- /footer -->

                </div>
                <!-- /content area -->

            </div>
            <!-- /main content -->

        </div>
        <!-- /page content -->

    </div>
    <!-- /page container -->

</body>
</html>
