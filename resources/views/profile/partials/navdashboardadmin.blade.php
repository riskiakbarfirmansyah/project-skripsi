<!DOCTYPE html>
<html lang="en">
    <link rel="stylesheet" href="{{ asset('image/LOGO RK.png') }}" rel="SHORTCUT ICON" />
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ env("APP_NAME") }} - Dashboard</title>
</head>
<body>
    <!-- Page Wrapper -->
    <div id="wrapper" class="flex h-screen bg-gray-50">
        <!-- Sidebar -->
        <ul class="navbar-nav sidebar sidebar-dark accordion" style="background-color: #9b8af4;" id="accordionSidebar"> 


        </ul>
    </div>
    <!-- End of Content Wrapper -->

    <!-- JavaScript untuk toggle menu -->
    <script>
        function toggleAccessMenu() {
            const menu = document.getElementById('accessMenu');
            const icon = document.getElementById('accessMenuIcon');
            
            menu.classList.toggle('hidden');
            icon.classList.toggle('rotate-180');
        }
    </script>
</body>
</html>