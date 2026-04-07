<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SOMS - Supplier Order Management System')</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff', 100: '#dbeafe', 200: '#bfdbfe', 300: '#93c5fd',
                            400: '#60a5fa', 500: '#3b82f6', 600: '#2563eb', 700: '#1d4ed8',
                            800: '#1e40af', 900: '#1e3a8a',
                        },
                        success: { 50: '#f0fdf4', 100: '#dcfce7', 500: '#22c55e', 600: '#16a34a', 700: '#15803d' },
                        warning: { 50: '#fffbeb', 100: '#fef3c7', 500: '#f59e0b', 600: '#d97706' },
                        error: { 50: '#fef2f2', 100: '#fee2e2', 500: '#ef4444', 600: '#dc2626' },
                        neutral: {
                            50: '#f9fafb', 100: '#f3f4f6', 200: '#e5e7eb', 300: '#d1d5db',
                            400: '#9ca3af', 500: '#6b7280', 600: '#4b5563', 700: '#374151',
                            800: '#1f2937', 900: '#111827',
                        },
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    
    <!-- Custom Sidebar Transition Styles -->
    <style>
        .sidebar-expanded { margin-left: 240px; }
        .sidebar-collapsed { margin-left: 52px; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-neutral-50 text-neutral-900 font-sans antialiased min-h-screen">
    <div class="flex min-h-screen group" id="layoutWrapper">
        <!-- Sidebar Component -->
        @include('components.sidebar')

        <!-- Main Wrapper -->
        <div id="mainWrapper" class="flex-1 flex flex-col min-h-screen transition-all duration-300 sidebar-expanded group-[.collapsed]:sidebar-collapsed">
            <!-- Global Header Component (matching Header.js) -->
            @include('components.header')

            <!-- Page Header (Page Specific Title/Actions) -->
            @include('components.page-header')

            <main class="flex-1 p-6">
                @yield('content')
            </main>
        </div>
    </div>

</body>
</html>
