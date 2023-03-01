<!DOCTYPE HTML>
      <html lang="<?php echo e(app()->getLocale()); ?>">
 <head>
    
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-9" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>" />
    <meta name="poppins" content="all,follow">
    <input type="hidden" name="base_url" id="base_url" value="<?php echo url('/'); ?>" />
    <title>Candidate & Counting  Management System</title>
    <meta name="description" content="">
    <meta name="keywords" content="">
    
    <!-- Bootstrap CSS-->
    <link rel="stylesheet" href="<?php echo e(asset('admintheme/vendor/bootstrap/css/bootstrap.min.css')); ?>">
    <!-- Font Awesome CSS-->
    <link rel="stylesheet" href="<?php echo e(asset('admintheme/vendor/font-awesome/css/font-awesome.min.css')); ?>">
    <!-- Fontastic Custom icon font-->
    <link rel="stylesheet" href="<?php echo e(asset('admintheme/css/fontastic.css')); ?>">
    <!-- Google fonts - Poppins -->
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet">
    <!-- jQuery Circle-->
   <link rel="stylesheet" href="<?php echo e(asset('admintheme/css/grasp_mobile_progress_circle-1.0.0.min.css')); ?>">
    <!-- Custom Scrollbar-->
    <link rel="stylesheet" href="<?php echo e(asset('admintheme/vendor/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.css')); ?>">
    <!-- theme stylesheet-->
    <link rel="stylesheet" href="<?php echo e(asset('admintheme/css/style.red.css')); ?>" id="theme-stylesheet">
    <!-- Custom stylesheet - for your changes-->
    <link rel="stylesheet" href="<?php echo e(asset('admintheme/css/custom.css')); ?>">
    <!-- Favicon-->
    <link rel="shortcut icon" href="<?php echo e(asset('admintheme/img/favicon.ico')); ?>">
    
    <link href="<?php echo url('admintheme/css/jquery.toast.css'); ?>" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo e(asset('admintheme/css/animate.css')); ?>">
    
  <!-- Scripts -->
   <script>
            window.Laravel = <?php echo json_encode([
                  'csrfToken' => csrf_token(),
                  ]); ?>
   </script>
  <script type="text/javascript">
        var APP_URL = <?php echo json_encode(url('/')); ?>

        var csrf  = <?php echo json_encode([ csrf_token(),]); ?>
  </script>
</head>

<body>
  <!-- container section start -->
   <!--main content start-->
    <?php echo $__env->yieldContent('content'); ?>
    <!--main content end-->
  
  
<!-- JavaScript files-->
    <script src="<?php echo e(asset('admintheme/vendor/jquery/jquery.min.js')); ?>"></script>
    <script src="<?php echo e(asset('admintheme/vendor/popper.js/umd/popper.min.js')); ?>"> </script>
    <script src="<?php echo e(asset('admintheme/vendor/bootstrap/js/bootstrap.min.js')); ?>"></script>
    <script src="<?php echo e(asset('admintheme/js/grasp_mobile_progress_circle-1.0.0.min.js')); ?>"></script>
    <script src="<?php echo e(asset('admintheme/vendor/jquery.cookie/jquery.cookie.js')); ?>"> </script>
    <script src="<?php echo e(asset('admintheme/vendor/chart.js/Chart.min.js')); ?>"></script>
    <script src="<?php echo e(asset('admintheme/vendor/jquery-validation/jquery.validate.min.js')); ?>"></script>
    <script src="<?php echo e(asset('admintheme/vendor/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.concat.min.js')); ?>"></script>
    <!-- Main File-->
    <script src="<?php echo e(asset('admintheme/js/front.js')); ?>"></script>
    <script src="<?php echo url('admintheme/js/jquery.toast.js'); ?>"></script>
    <?php echo $__env->yieldContent('script'); ?>;
  </body>
</html><?php /**PATH E:\xampp\htdocs\suvidha\resources\views/admin/layouts/login.blade.php ENDPATH**/ ?>