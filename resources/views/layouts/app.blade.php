<!DOCTYPE html>
@php
use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\Auth;
@endphp
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Pace Driving School - Professional Driving Lessons</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('img/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('img/favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('img/apple-touch-icon.png') }}">
    <link rel="shortcut icon" href="{{ asset('img/favicon.ico') }}">
    <link rel="icon" type="image/png" href="{{ asset('img/Logo.png') }}">
    <link rel="shortcut icon" href="{{ asset('img/Logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('img/Logo.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>



    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Livewire Styles -->
    @livewireStyles

<style>
    [x-cloak] {
        display: none !important;
    }

    html {
        scroll-behavior: smooth;
    }

    body {
        background: #FFFFFF;
        color: #000000;
        font-family: 'Arial', sans-serif;
        overflow-x: hidden;
    }

    main {
        min-height: calc(100vh - 200px);
    }

    /* ===== TOP NAVIGATION BAR ===== */
    .nav-top-bar {
        background: #1a3b6a;
        color: #FFFFFF;
        padding: 0.5rem 0;
        font-size: 0.75rem;
    }

    .top-nav-compact {
        max-width: 1280px;
        margin: 0 auto;
        padding: 0 1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .mobile-contact {
        display: flex;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .mobile-contact a {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: white;
        text-decoration: none;
        transition: color 0.3s;
        white-space: nowrap;
    }

    .mobile-contact a:hover {
        color: #FFD700;
    }

    .mobile-social {
        display: flex;
        gap: 0.75rem;
        align-items: center;
        flex-wrap: wrap;
    }

    .mobile-social a {
        color: white;
        transition: color 0.3s;
        padding: 0.25rem;
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 24px;
    }

    .mobile-social a:hover {
        color: #FFD700;
        transform: scale(1.1);
    }

    /* Mobile Responsive Top Bar */
    @media (max-width: 640px) {
        .nav-top-bar {
            padding: 0.4rem 0;
        }

        .top-nav-compact {
            justify-content: center;
            gap: 0.75rem;
        }

        .mobile-contact {
            gap: 0.75rem;
            justify-content: center;
        }

        .mobile-contact a span {
            display: none;
        }

        .mobile-contact a {
            gap: 0;
        }

        .mobile-social {
            gap: 0.5rem;
        }
    }

    @media (max-width: 480px) {
        .top-nav-compact {
            flex-direction: column;
            gap: 0.5rem;
        }

        .mobile-contact {
            gap: 1rem;
        }

        .mobile-social {
            gap: 0.75rem;
        }
    }

    @media (max-width: 360px) {
        .mobile-contact {
            gap: 0.75rem;
        }

        .mobile-social {
            gap: 0.5rem;
        }

        .mobile-social a {
            font-size: 0.9rem;
        }
    }

    /* ===== MAIN NAVIGATION ===== */
    .nav-main-bar {
        background: #1a3b6a;
        color: #FFFFFF;
        position: sticky;
        top: 0;
        z-index: 50;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .nav-container {
        max-width: 1280px;
        margin: 0 auto;
        padding: 0 1rem;
    }

    .nav-inner {
        display: flex;
        justify-content: space-between;
        align-items: center;
        height: 5rem;
    }

    .nav-logo {
        flex-shrink: 0;
    }

    .nav-logo img {
        height: 3.5rem;
        width: auto;
    }

    .nav-links {
        display: none;
    }

    .nav-auth {
        display: none;
    }

    .mobile-menu-btn {
        display: flex;
        align-items: center;
        background: none;
        border: none;
        color: white;
        font-size: 1.5rem;
        cursor: pointer;
        padding: 0.5rem;
        border-radius: 0.375rem;
        transition: background-color 0.3s;
    }

    .mobile-menu-btn:hover {
        background: rgba(255, 255, 255, 0.1);
    }

    /* Desktop Navigation */
    @media (min-width: 768px) {
        .nav-links {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            margin-left: 2.5rem;
        }

        .nav-link {
            color: white;
            text-decoration: none;
            padding: 0.75rem 1rem;
            border-radius: 0.375rem;
            transition: background-color 0.3s;
            font-weight: 500;
            white-space: nowrap;
        }

        .nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .nav-auth {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-left: 1.5rem;
        }

        .mobile-menu-btn {
            display: none;
        }
    }

    /* Dropdown Styles */
    .dropdown {
        position: relative;
    }

    .dropdown-btn {
        display: flex;
        align-items: center;
        gap: 0.25rem;
        background: none;
        border: none;
        color: white;
        padding: 0.75rem 1rem;
        border-radius: 0.375rem;
        cursor: pointer;
        transition: background-color 0.3s;
        font-weight: 500;
        font-family: inherit;
        font-size: inherit;
    }

    .dropdown-btn:hover {
        background: rgba(255, 255, 255, 0.1);
    }

    .dropdown-content {
        position: absolute;
        top: 100%;
        left: 0;
        background: #1a3b6a;
        min-width: 14rem;
        border-radius: 0.375rem;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px);
        transition: all 0.3s ease;
        z-index: 50;
        border: 1px solid #0e1f38;
    }

    .dropdown:hover .dropdown-content {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .dropdown-content a {
        display: block;
        padding: 0.75rem 1rem;
        color: white;
        text-decoration: none;
        transition: background-color 0.3s;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        font-size: 0.875rem;
    }

    .dropdown-content a:last-child {
        border-bottom: none;
    }

    .dropdown-content a:hover {
        background: rgba(255, 255, 255, 0.1);
    }

    /* ===== MOBILE MENU ===== */
    .mobile-menu {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100vh;
        background: #1a3b6a;
        z-index: 1000;
        transform: translateX(-100%);
        transition: transform 0.3s ease;
        overflow-y: auto;
    }

    .mobile-menu.open {
        transform: translateX(0);
    }

    .mobile-menu-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .mobile-menu-close {
        background: none;
        border: none;
        color: white;
        font-size: 1.5rem;
        cursor: pointer;
        padding: 0.5rem;
    }

    .mobile-nav-links {
        padding: 1rem;
    }

    .mobile-nav-item {
        margin-bottom: 0.5rem;
    }

    .mobile-nav-link {
        display: block;
        padding: 0.75rem 1rem;
        color: white;
        text-decoration: none;
        border-radius: 0.375rem;
        transition: background-color 0.3s;
        font-weight: 500;
    }

    .mobile-nav-link:hover {
        background: rgba(255, 255, 255, 0.1);
    }

    .mobile-dropdown-btn {
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
        background: none;
        border: none;
        color: white;
        padding: 0.75rem 1rem;
        border-radius: 0.375rem;
        cursor: pointer;
        transition: background-color 0.3s;
        font-weight: 500;
        font-family: inherit;
        font-size: inherit;
    }

    .mobile-dropdown-btn:hover {
        background: rgba(255, 255, 255, 0.1);
    }

    .mobile-dropdown-content {
        padding-left: 1rem;
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease;
    }

    .mobile-dropdown-content.open {
        max-height: 500px;
    }

    .mobile-dropdown-content a {
        display: block;
        padding: 0.5rem 1rem;
        color: white;
        text-decoration: none;
        border-radius: 0.375rem;
        transition: background-color 0.3s;
        font-size: 0.875rem;
        margin-bottom: 0.25rem;
    }

    .mobile-dropdown-content a:hover {
        background: rgba(255, 255, 255, 0.1);
    }

    .mobile-auth {
        padding: 1rem;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    /* ===== MODALS ===== */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.7);
        z-index: 1000;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }

    .modal-container {
        background: white;
        border-radius: 0.75rem;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        width: 100%;
        max-width: 32rem;
        max-height: 90vh;
        overflow-y: auto;
        position: relative;
    }

    .modal-header {
        background: #1a3b6a;
        color: white;
        padding: 1.5rem;
        border-radius: 0.75rem 0.75rem 0 0;
        position: relative;
    }

    .modal-close {
        position: absolute;
        top: 1rem;
        right: 1rem;
        background: none;
        border: none;
        color: white;
        font-size: 1.5rem;
        cursor: pointer;
        padding: 0.25rem;
    }

    .modal-body {
        padding: 1.5rem;
    }

    /* ===== BUTTONS ===== */
    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.75rem 1.5rem;
        border-radius: 0.375rem;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        font-family: inherit;
        font-size: 0.875rem;
        white-space: nowrap;
    }

    .btn-primary {
        background: #FFD700;
        color: #1a3b6a;
        font-weight: 600;
    }

    .btn-primary:hover {
        background: #FFC400;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(255, 215, 0, 0.3);
    }

    .btn-secondary {
        background: white;
        color: #1a3b6a;
        border: 1px solid #1a3b6a;
    }

    .btn-secondary:hover {
        background: #1a3b6a;
        color: white;
    }

    /* ===== FORM STYLES ===== */
    .form-input {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        background: white;
        color: #000000;
        transition: all 0.3s ease;
    }

    .form-input:focus {
        border-color: #1a3b6a;
        box-shadow: 0 0 0 3px rgba(26, 59, 106, 0.3);
        outline: none;
    }

    /* ===== FOOTER ===== */
    .footer-bg {
        background: #0e1f38;
        color: #FFFFFF;
        padding: 3rem 0 1rem;
    }

    .footer-container {
        max-width: 1280px;
        margin: 0 auto;
        padding: 0 1rem;
    }

    .footer-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 2rem;
        margin-bottom: 2rem;
    }

    .footer-col h4 {
        font-size: 1.125rem;
        font-weight: 600;
        margin-bottom: 1rem;
        position: relative;
        padding-bottom: 0.5rem;
    }

    .footer-col h4::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 3rem;
        height: 2px;
        background: #FFD700;
    }

    .footer-links {
        list-style: none;
    }

    .footer-links li {
        margin-bottom: 0.5rem;
    }

    .footer-links a {
        color: #d1d5db;
        text-decoration: none;
        transition: color 0.3s;
        font-size: 0.875rem;
    }

    .footer-links a:hover {
        color: #FFD700;
        padding-left: 0.25rem;
    }

    .footer-bottom {
        text-align: center;
        padding-top: 2rem;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        color: #9ca3af;
        font-size: 0.875rem;
    }

    /* Footer Responsive */
    @media (min-width: 768px) {
        .footer-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (min-width: 1024px) {
        .footer-grid {
            grid-template-columns: repeat(4, 1fr);
        }
    }

    /* ===== UTILITY CLASSES ===== */
    .text-blue-dark { color: #1a3b6a; }
    .text-blue-light { color: #2567ac; }
    .bg-blue-light { background: #2567ac; }
    .bg-blue-dark { background: #1a3b6a; }
    .border-blue-light { border-color: #2567ac; }

    .emergency-banner {
        background: #FFD700;
        color: #1a3b6a;
        padding: 0.75rem 0;
        text-align: center;
        font-size: 0.875rem;
        font-weight: 600;
    }

    /* ===== RESPONSIVE HELPERS ===== */
    .mobile-only {
        display: block;
    }

    .desktop-only {
        display: none;
    }

    @media (min-width: 768px) {
        .mobile-only {
            display: none;
        }

        .desktop-only {
            display: block;
        }
    }

    /* ===== SWIPER OVERRIDES ===== */
    .hero-swiper {
        width: 100%;
        height: 400px;
    }

    @media (min-width: 768px) {
        .hero-swiper {
            height: 500px;
        }
    }

    @media (min-width: 1024px) {
        .hero-swiper {
            height: 600px;
        }
    }

    /* ===== QUICK LINKS SECTION ===== */
    .quick-links-section {
        background: linear-gradient(135deg, #1a3b6a 0%, #2567ac 50%, #3b82f6 100%);
        position: relative;
        overflow: hidden;
        padding: 3rem 0;
    }

    .quick-links-section::before {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 100px;
        background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1200 120' preserveAspectRatio='none'%3E%3Cpath d='M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z' fill='%23ffffff'%3E%3C/path%3E%3C/svg%3E");
        background-size: cover;
        background-position: center;
    }

    .quick-links-container {
        max-width: 1280px;
        margin: 0 auto;
        padding: 0 1rem;
        position: relative;
        z-index: 1;
    }

    .quick-links-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }

    .quick-link-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 1rem;
        padding: 1.5rem 1rem;
        text-align: center;
        transition: all 0.3s ease;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        text-decoration: none;
    }

    .quick-link-card:hover {
        transform: translateY(-4px);
        background: rgba(255, 255, 255, 1);
        box-shadow: 0 16px 48px rgba(0, 0, 0, 0.15);
    }

    .quick-link-icon {
        width: 60px;
        height: 60px;
        margin: 0 auto 1rem;
        border-radius: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        transition: all 0.3s ease;
        background: #FFD700;
        color: #1a3b6a;
    }

    .quick-link-card:hover .quick-link-icon {
        transform: scale(1.1);
        background: #1a3b6a;
        color: #FFD700;
    }

    .quick-link-text {
        font-weight: 600;
        color: #1a3b6a;
        font-size: 0.8rem;
        line-height: 1.3;
    }

    /* Quick Links Responsive */
    @media (min-width: 480px) {
        .quick-links-grid {
            grid-template-columns: repeat(3, 1fr);
        }

        .quick-link-icon {
            width: 70px;
            height: 70px;
            font-size: 2rem;
        }
    }

    @media (min-width: 768px) {
        .quick-links-grid {
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
        }

        .quick-link-card {
            padding: 2rem 1rem;
        }

        .quick-link-icon {
            width: 80px;
            height: 80px;
            font-size: 2.5rem;
        }

        .quick-link-text {
            font-size: 0.9rem;
        }
    }

    @media (min-width: 1024px) {
        .quick-links-grid {
            grid-template-columns: repeat(6, 1fr);
        }
    }


    /* Add this to your existing CSS in the <style> section */

/* Modal Responsive Improvements */
@media (max-width: 640px) {
    .modal-container {
        margin: 1rem;
        max-height: 85vh;
    }

    .modal-body {
        padding: 1rem;
    }

    .modal-header {
        padding: 1rem;
    }

    .modal-header h2 {
        font-size: 1.5rem;
    }
}

@media (max-width: 480px) {
    .modal-container {
        margin: 0.5rem;
        border-radius: 0.5rem;
    }

    .grid.grid-cols-1.md\:grid-cols-2 {
        grid-template-columns: 1fr;
    }

    .modal-body {
        padding: 0.75rem;
    }
}

/* Form input improvements for mobile */
.form-input {
    font-size: 16px; /* Prevents zoom on iOS */
}

/* Ensure proper spacing on very small screens */
@media (max-width: 360px) {
    .modal-body {
        padding: 0.5rem;
    }

    .btn {
        padding: 0.625rem 1.25rem;
        font-size: 0.8rem;
    }
}

/* Loading states for buttons */
.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none !important;
}

.btn-loading {
    position: relative;
    color: transparent;
}

.btn-loading::after {
    content: '';
    position: absolute;
    width: 1rem;
    height: 1rem;
    top: 50%;
    left: 50%;
    margin-left: -0.5rem;
    margin-top: -0.5rem;
    border: 2px solid transparent;
    border-top-color: currentColor;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}


.nav-brand {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.nav-logo img {
  width: 70px; /* Adjust size as needed */
  height: 70px;
  object-fit: cover;
  border-radius: 50%; /* Makes it circular */
  background-color: #ffffffff;
  padding: 5px;
  box-shadow: 0 0 5px rgba(0,0,0,0.1);
}

    /* Driving School Icon Colors */
    .icon-courses { background: linear-gradient(135deg, #e6f2ff 0%, #b3d9ff 100%); color: #0066cc; }
    .icon-lessons { background: linear-gradient(135deg, #e6fff2 0%, #b3ffd9 100%); color: #00a86b; }
    .icon-theory { background: linear-gradient(135deg, #fff8e6 0%, #ffecb3 100%); color: #ff9900; }
    .icon-practical { background: linear-gradient(135deg, #ffe6e6 0%, #ffb3b3 100%); color: #cc0000; }
    .icon-payment { background: linear-gradient(135deg, #f0e6ff 0%, #d9b3ff 100%); color: #6633cc; }
    .icon-resources { background: linear-gradient(135deg, #e6f0ff 0%, #b3d1ff 100%); color: #3366cc; }
    .icon-instructor { background: linear-gradient(135deg, #e6fff0 0%, #b3ffd1 100%); color: #00cc66; }
    .icon-student { background: linear-gradient(135deg, #fff0e6 0%, #ffd1b3 100%); color: #ff6600; }
    .icon-faq { background: linear-gradient(135deg, #e6f9ff 0%, #b3ecff 100%); color: #0099cc; }
    .icon-testimonials { background: linear-gradient(135deg, #fff0f5 0%, #ffd1e6 100%); color: #cc3366; }
    .icon-contacts { background: linear-gradient(135deg, #f0fff0 0%, #d1ffd1 100%); color: #33cc33; }
    .icon-locations { background: linear-gradient(135deg, #f0f0ff 0%, #d1d1ff 100%); color: #6666cc; }

    /* Fade-in animation for sections */
    .fade-in-section {
        opacity: 0;
        transform: translateY(20px);
        transition: opacity 0.6s ease, transform 0.6s ease;
    }

    .fade-in-section.is-visible {
        opacity: 1;
        transform: translateY(0);
    }
</style>
<style>
/* Professional Portal Button Styles */
.portal-container {
    position: relative;
}

.portal-btn {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    border-radius: 16px;
    padding: 12px 20px;
    color: white;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
    min-width: 180px;
}

.portal-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 35px rgba(102, 126, 234, 0.4);
    background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
}

.portal-btn:active {
    transform: translateY(0);
}

.portal-btn-content {
    display: flex;
    align-items: center;
    gap: 12px;
    position: relative;
    z-index: 2;
}

.portal-icon {
    width: 40px;
    height: 40px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.3);
}

.portal-icon i {
    font-size: 1.1rem;
}

.portal-text {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    flex: 1;
}

.portal-label {
    font-size: 0.9rem;
    font-weight: 700;
    line-height: 1.2;
    text-align: left;
}

.portal-subtitle {
    font-size: 0.7rem;
    opacity: 0.9;
    font-weight: 500;
    margin-top: 2px;
}

.portal-arrow {
    opacity: 0;
    transform: translateX(-5px);
    transition: all 0.3s ease;
}

.portal-btn:hover .portal-arrow {
    opacity: 1;
    transform: translateX(0);
}

.portal-hover-effect {
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.6s ease;
}

.portal-btn:hover .portal-hover-effect {
    left: 100%;
}

/* Mobile Portal Button */
.mobile-portal-section {
    margin: 20px 0;
    padding: 0 16px;
}

.mobile-portal-btn {
    width: 100%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    border-radius: 16px;
    padding: 16px 20px;
    color: white;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
}

.mobile-portal-btn:active {
    transform: scale(0.98);
}

.mobile-portal-content {
    display: flex;
    align-items: center;
    gap: 16px;
}

.mobile-portal-icon {
    width: 44px;
    height: 44px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid rgba(255, 255, 255, 0.3);
}

.mobile-portal-icon i {
    font-size: 1.2rem;
}

.mobile-portal-text {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    flex: 1;
}

.mobile-portal-label {
    font-size: 1rem;
    font-weight: 700;
    line-height: 1.2;
    text-align: left;
}

.mobile-portal-subtitle {
    font-size: 0.8rem;
    opacity: 0.9;
    font-weight: 500;
    margin-top: 2px;
}

.mobile-portal-arrow {
    opacity: 0.7;
}

.mobile-portal-arrow i {
    font-size: 0.9rem;
}

/* Mobile User Section */
.mobile-user-section {
    margin: 20px 0;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    padding-top: 20px;
}

.mobile-user-info {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 0 16px 16px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.mobile-user-avatar {
    width: 44px;
    height: 44px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}

.mobile-user-avatar i {
    font-size: 1.2rem;
}

.mobile-user-details {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
}

.mobile-user-name {
    font-weight: 600;
    color: white;
    font-size: 0.95rem;
}

.mobile-user-role {
    font-size: 0.8rem;
    opacity: 0.8;
    color: white;
    text-transform: capitalize;
}

.mobile-user-links {
    padding: 8px 0;
}

.mobile-user-link {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    color: rgba(255, 255, 255, 0.9);
    text-decoration: none;
    transition: all 0.3s ease;
    border: none;
    background: none;
    width: 100%;
    text-align: left;
    font-size: 0.9rem;
}

.mobile-user-link:hover {
    background: rgba(255, 255, 255, 0.1);
    color: white;
}

.mobile-user-link i {
    width: 20px;
    text-align: center;
    opacity: 0.8;
}

.mobile-logout-btn {
    color: #f87171 !important;
}

.mobile-logout-btn:hover {
    background: rgba(248, 113, 113, 0.1) !important;
    color: #f87171 !important;
}

/* Enhanced Mobile Menu Styles */
.mobile-menu {
    position: fixed;
    top: 0;
    right: -100%;
    width: 100%;
    max-width: 400px;
    height: 100vh;
    background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
    z-index: 1000;
    transition: right 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    overflow-y: auto;
}

.mobile-menu.open {
    right: 0;
}

.mobile-menu-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 20px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    background: rgba(255, 255, 255, 0.05);
}

.mobile-menu-close {
    background: rgba(255, 255, 255, 0.1);
    border: none;
    border-radius: 12px;
    width: 44px;
    height: 44px;
    color: white;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
}

.mobile-menu-close:hover {
    background: rgba(255, 255, 255, 0.2);
}

.mobile-nav-links {
    padding: 20px 0;
}

.mobile-nav-link {
    display: block;
    padding: 16px 24px;
    color: rgba(255, 255, 255, 0.9);
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
    border-left: 4px solid transparent;
}

.mobile-nav-link:hover {
    background: rgba(255, 255, 255, 0.1);
    color: white;
    border-left-color: #f59e0b;
}

/* Mobile Dropdown Enhancements */
.mobile-nav-item {
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.mobile-dropdown-btn {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 24px;
    color: rgba(255, 255, 255, 0.9);
    background: none;
    border: none;
    cursor: pointer;
    font-weight: 500;
    transition: all 0.3s ease;
    border-left: 4px solid transparent;
}

.mobile-dropdown-btn:hover {
    background: rgba(255, 255, 255, 0.1);
    color: white;
}

.mobile-dropdown-content {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    background: rgba(0, 0, 0, 0.2);
}

.mobile-dropdown-content.open {
    max-height: 300px;
}

.mobile-dropdown-content a {
    display: block;
    padding: 12px 24px 12px 48px;
    color: rgba(255, 255, 255, 0.8);
    text-decoration: none;
    font-size: 0.9rem;
    transition: all 0.3s ease;
    border-left: 4px solid transparent;
}

.mobile-dropdown-content a:hover {
    background: rgba(255, 255, 255, 0.1);
    color: white;
    border-left-color: #f59e0b;
}

/* Backdrop for mobile menu */
.mobile-menu-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 999;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
}

.mobile-menu-backdrop.active {
    opacity: 1;
    visibility: visible;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .portal-btn {
        min-width: auto;
        padding: 10px 16px;
    }

    .portal-text {
        display: none;
    }

    .portal-arrow {
        display: none;
    }

    .portal-btn-content {
        gap: 0;
        justify-content: center;
    }

    .portal-icon {
        width: 36px;
        height: 36px;
    }
}

@media (max-width: 480px) {
    .mobile-menu {
        max-width: 100%;
    }
}
</style>
</head>

<body x-data="{
    signupOpen: false,
    loginOpen: false,
    mobileMenuOpen: false,
    mobileDropdowns: {}
}" class="font-sans antialiased body-bg">

    <!-- Top Navigation Bar -->
    <div class="nav-top-bar" style="background-color:#1a3b6a;">
        <div class="top-nav-compact">
            <!-- Contact Information -->
            <div class="mobile-contact">
                <a href="tel:+254728725559">
                    <i class="fas fa-phone-alt"></i>
                    <span class="desktop-only">+254 728 725 559</span>
                </a>
                <a href="mailto:info@pacedrivingschool.co.ke">
                    <i class="fas fa-envelope"></i>
                    <span class="desktop-only">info@pacedrivingschool.co.ke</span>
                </a>
            </div>

            <!-- Social Links -->

        </div>
    </div>

<!-- Main Navigation -->
<nav class="nav-main-bar">
    <div class="nav-container">
        <div class="nav-inner">
            <!-- Logo -->
            <div class="nav-brand">
                <div class="nav-logo">
                    <a href="{{ route('home') }}">
                        <img src="{{ asset('img/Logo.png') }}" alt="Pace Driving School Logo">
                    </a>
                </div>
                <div class="text-white">
                    <h1 class="text-xl font-bold">Pace Driving School</h1>
                    <p class="text-sm opacity-90">Teaching You to Take Control</p>
                </div>
            </div>

            <!-- Desktop Navigation -->
            <div class="nav-links">
                <a href="{{ route('home') }}" class="nav-link">Home</a>

                <div class="dropdown">
                    <button class="dropdown-btn">
                        About Us
                        <i class="fas fa-chevron-down ml-1" style="font-size: 0.75rem;"></i>
                    </button>
                    <div class="dropdown-content">
                        <a href="#">Our Story</a>
                        <!-- <a href="{{ url('instructors') }}">Our Instructors</a>
                        <a href="{{ url('mission') }}">Mission & Vision</a>
                        <a href="{{ url('success') }}">Success Stories</a> -->
                    </div>
                </div>
                <a href="{{ route('pages.courses') }}" class="nav-link">Courses</a>
            </div>

            <!-- Desktop Auth -->
            <div class="nav-auth">
                @auth
                    <livewire:navigation.user-dropdown />
                @else
                    <!-- Professional Portal Button -->
                    <div class="portal-container">
                        <button @click="loginOpen = true" class="portal-btn">
                            <div class="portal-btn-content">

                                <div class="portal-text">
                                    <span class="portal-label">Access Account</span>
                                </div>
                                <div class="portal-arrow">
                                    <i class="fas fa-arrow-right"></i>
                                </div>
                            </div>
                            <div class="portal-hover-effect"></div>
                        </button>
                    </div>
                @endauth
            </div>

            <!-- Mobile Menu Button -->
            <button @click="mobileMenuOpen = true" class="mobile-menu-btn">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </div>
</nav>

<!-- Mobile Menu -->
<div class="mobile-menu" :class="{ 'open': mobileMenuOpen }">
    <div class="mobile-menu-header">
        <div class="nav-brand">
            <div class="nav-logo">
                <a href="{{ route('home') }}">
                    <img src="{{ asset('img/Logo.png') }}" alt="Pace Driving School Logo" class="h-10">
                </a>
            </div>
            <div class="text-white">
                <h1 class="text-lg font-bold">Pace Driving</h1>
            </div>
        </div>
        <button @click="mobileMenuOpen = false" class="mobile-menu-close">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <div class="mobile-nav-links">
        <a href="{{ route('home') }}" class="mobile-nav-link" @click="mobileMenuOpen = false">Home</a>

        <div class="mobile-nav-item">
            <button class="mobile-dropdown-btn" @click="mobileDropdowns.about = !mobileDropdowns.about">
                About Us
                <i class="fas fa-chevron-down transition-transform" :class="{ 'rotate-180': mobileDropdowns.about }"></i>
            </button>
            <div class="mobile-dropdown-content" :class="{ 'open': mobileDropdowns.about }">
                <a href="#" @click="mobileMenuOpen = false">Our Story</a>
            </div>
        </div>

        <a href="{{ route('pages.courses') }}" class="mobile-nav-link" @click="mobileMenuOpen = false">Courses</a>

         <!-- Mobile Auth Section -->
        @auth
            <!-- Mobile User Info -->
            <div class="mobile-user-section">
                <div class="mobile-user-info">
                    <div class="mobile-user-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="mobile-user-details">
                        <span class="mobile-user-name">{{ auth()->user()->name }}</span>
                        <span class="mobile-user-role">{{ auth()->user()->role ?? 'Student' }}</span>
                    </div>
                </div>
                <div class="mobile-user-links">
                    <a href="{{ route('dashboard') }}" class="mobile-user-link" @click="mobileMenuOpen = false">
                        <i class="fas fa-tachometer-alt"></i>
                        Dashboard
                    </a>
                    <a href="{{ route('profile.edit') }}" class="mobile-user-link" @click="mobileMenuOpen = false">
                        <i class="fas fa-user-cog"></i>
                        Profile Settings
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <button type="submit" class="mobile-user-link mobile-logout-btn w-full text-left">
                            <i class="fas fa-sign-out-alt"></i>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        @else
            <!-- Mobile Portal Button -->
            <div class="mobile-portal-section">
                <button @click="loginOpen = true; mobileMenuOpen = false" class="mobile-portal-btn">
                    <div class="mobile-portal-content">
                        <div class="mobile-portal-icon">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <div class="mobile-portal-text">
                            <span class="mobile-portal-label">Access Account</span>
                        </div>
                        <div class="mobile-portal-arrow">
                            <i class="fas fa-arrow-right"></i>
                        </div>
                    </div>
                </button>
            </div>
        @endguest
            </div>
    </div>

    <!-- Sign Up Modal -->
    <div x-show="signupOpen" x-cloak class="modal-overlay">
        <div class="modal-container">
            <div class="modal-header">
                <h2 class="text-2xl font-bold">Join Pace Driving School</h2>
                <button @click="signupOpen = false" class="modal-close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="mb-6 justify-center flex">
                    <a href="{{ route('home') }}" class="flex items-center">
                        <img src="{{ asset('img/Logo.png') }}" class="h-16 w-auto">
                    </a>
                </div>

                <x-validation-errors class="mb-4" />

                <form method="POST" action="{{ route('register') }}" id="registrationForm">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <x-label for="name" value="Full Name *" />
                            <x-input id="name" type="text" name="name" :value="old('name')" required autofocus class="form-input w-full mt-1" />
                        </div>
                        <div>
                            <x-label for="email" value="Email Address *" />
                            <x-input id="email" type="email" name="email" :value="old('email')" required class="form-input w-full mt-1" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <x-label for="phone" value="Phone Number *" />
                            <x-input id="phone" type="tel" name="phone" :value="old('phone')" required class="form-input w-full mt-1" placeholder="+254 XXX XXX XXX" />
                        </div>
                        <div>
                            <x-label for="id_number" value="ID Number *" />
                            <x-input id="id_number" type="text" name="id_number" :value="old('id_number')" required class="form-input w-full mt-1" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <x-label for="password" value="Password *" />
                            <x-input id="password" type="password" name="password" required autocomplete="new-password" class="form-input w-full mt-1" />
                        </div>
                        <div>
                            <x-label for="password_confirmation" value="Confirm Password *" />
                            <x-input id="password_confirmation" type="password" name="password_confirmation" required class="form-input w-full mt-1" />
                        </div>
                    </div>

                    <div class="mb-4">
                        <x-label for="address" value="Physical Address *" />
                        <textarea id="address" name="address" required class="form-input w-full mt-1" rows="3" placeholder="Enter your complete physical address">{{ old('address') }}</textarea>
                    </div>

                    <div class="mb-6">
                        <label class="flex items-center">
                            <input type="checkbox" name="terms" required class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-600">
                                I agree to the <a href="#" class="text-blue-600 hover:text-blue-800">Terms of Service</a> and <a href="#" class="text-blue-600 hover:text-blue-800">Privacy Policy</a>
                            </span>
                        </label>
                    </div>

                    <div class="mt-6">
                        <button type="submit" class="w-full btn-primary font-medium py-3 px-4 rounded-lg transition duration-200">
                            Start Driving Journey
                        </button>
                    </div>
                </form>

                <div class="mt-6 text-center">
                    <span class="text-sm text-gray-600">
                        Already have an account?
                        <button type="button" @click="signupOpen = false; loginOpen = true" class="text-blue-600 hover:text-blue-800 font-medium transition-colors ml-1">
                            Log in
                        </button>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Login Modal -->
    <div x-show="loginOpen" x-cloak class="modal-overlay">
        <div class="modal-container">
            <div class="modal-header">
                <h2 class="text-2xl font-bold">Student Portal</h2>
                <button @click="loginOpen = false" class="modal-close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="mb-6 justify-center flex">
                    <a href="{{ route('home') }}" class="flex items-center">
                        <img src="{{ asset('img/Logo.png') }}" class="h-16 w-auto">
                    </a>
                </div>

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="mb-4">
                        <x-label for="login_email" value="Email Address" />
                        <x-input id="login_email" class="form-input block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="email" placeholder="Enter your email address" />
                    </div>

                    <div class="mb-4">
                        <x-label for="login_password" value="Password" />
                        <x-input id="login_password" class="form-input block mt-1 w-full" type="password" name="password" required autocomplete="current-password" placeholder="Enter your password" />
                    </div>

                    <div class="flex items-center justify-between mb-6">
                        <label class="flex items-center">
                            <input type="checkbox" name="remember" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-600">Remember me</span>
                        </label>

                        @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-sm text-blue-600 hover:text-blue-800 transition-colors">
                            Forgot password?
                        </a>
                        @endif
                    </div>

                    <div class="mb-6">
                        <button type="submit" class="w-full btn-primary font-medium py-3 px-4 rounded-lg transition duration-200">
                            Access Portal
                        </button>
                    </div>
                </form>

                <div class="relative mb-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white text-gray-500">New to Pace Driving School?</span>
                    </div>
                </div>

                <div class="text-center">
                    <button type="button" @click="loginOpen = false; signupOpen = true" class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 transition duration-200">
                        <i class="fas fa-user-plus text-blue-500 mr-2"></i>
                        Create New Account
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Page Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer-bg" style="background-color:#0e1f38;">
        <div class="footer-container">
            <div class="footer-grid">
                <!-- Company Info -->
                <div>
                    <div class="nav-brand mb-4">
                        <div class="nav-logo">
                            <a href="{{ route('home') }}">
                                <img src="{{ asset('img/Logo.png') }}" class="h-16 w-auto">
                            </a>
                        </div>
                        <div class="text-white">
                            <h1 class="text-xl font-bold">Pace Driving School</h1>
                            <p class="text-sm opacity-90">Teaching You to Take Control</p>
                        </div>
                    </div>
                    <p class="text-gray-300 text-sm leading-relaxed mb-4">
                        Your trusted partner in driving education for over 10 years. We're committed to creating expert, safe drivers through professional instruction and personalized training.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-300 hover:text-yellow-400 transition-colors">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="text-gray-300 hover:text-yellow-400 transition-colors">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="text-gray-300 hover:text-yellow-400 transition-colors">
                            <i class="fab fa-instagram"></i>
                        </a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div>
                    <h4>Quick Links</h4>
                    <ul class="footer-links">
                        <li><a href="{{ route('home') }}">Home</a></li>
                        <li><a href="{{ route('home') }}">About Us</a></li>
                        <li><a href="{{ route('pages.courses') }}">Courses</a></li>
                        <li><a href="{{ route('book-lessons') }}">Book Lessons</a></li>
                        <li><a href="{{ route('home') }}">Testimonials</a></li>
                    </ul>
                </div>

                <!-- Courses -->
                <div>
                    <h4>Our Courses</h4>
                    <ul class="footer-links">
                        <li><a href="{{route('pages.courses') }}">Beginner Course</a></li>
                        <li><a href="{{route('pages.courses') }}">Refresher Course</a></li>
                        <li><a href="{{route('pages.courses') }}">Commercial License</a></li>
                        <li><a href="{{route('pages.courses') }}">Motorcycle Training</a></li>
                        <li><a href="{{route('pages.courses') }}">Theory Test Prep</a></li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div>
                    <h4>Contact Us</h4>
                    <ul class="footer-links">
                        <li class="flex items-start">
                            <i class="fas fa-map-marker-alt mt-1 mr-3 text-yellow-400"></i>
                            <span>Nairobi, Kenya</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-phone-alt mr-3 text-yellow-400"></i>
                            <span>+254 728 725 559</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-envelope mr-3 text-yellow-400"></i>
                            <span>info@pacedrivingschool.co.ke</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-clock mr-3 text-yellow-400"></i>
                            <span>Mon - Sun: 7:00 AM - 7:00 PM</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Bottom Bar -->
            <div class="footer-bottom">
                <p>&copy; 2024 <strong>bAtasi</strong>. All rights reserved. | Drive Safe, Drive Expertly</p>
            </div>
        </div>
    </footer>
    <script>
        toastr.options = {
            closeButton: true,
            progressBar: true,
            timeOut: 3000,
            positionClass: "toast-top-right",
        };
    </script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Swiper
            const heroSwiper = new Swiper('.hero-swiper', {
                loop: true,
                autoplay: {
                    delay: 5000,
                    disableOnInteraction: false,
                },
                speed: 1000,
                effect: 'fade',
                fadeEffect: {
                    crossFade: true
                },
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
            });

            // Fade-in animation for sections
            const fadeInSections = document.querySelectorAll('.fade-in-section');

            const fadeInObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                    }
                });
            }, {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            });

            fadeInSections.forEach(section => {
                fadeInObserver.observe(section);
            });

            // Close mobile menu when clicking outside
            document.addEventListener('click', function(event) {
                const mobileMenu = document.querySelector('.mobile-menu');
                const mobileMenuBtn = document.querySelector('.mobile-menu-btn');

                if (mobileMenu.classList.contains('open') &&
                    !mobileMenu.contains(event.target) &&
                    !mobileMenuBtn.contains(event.target)) {
                    mobileMenu.classList.remove('open');
                }
            });

            // Close modals when clicking outside
            document.addEventListener('click', function(event) {
                const signupModal = document.querySelector('[x-show="signupOpen"]');
                const loginModal = document.querySelector('[x-show="loginOpen"]');

                if (signupModal && signupModal.style.display !== 'none' &&
                    !signupModal.querySelector('.modal-container').contains(event.target)) {
                    signupOpen = false;
                }

                if (loginModal && loginModal.style.display !== 'none' &&
                    !loginModal.querySelector('.modal-container').contains(event.target)) {
                    loginOpen = false;
                }
            });
        });

        // Close mobile menu on escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                const mobileMenu = document.querySelector('.mobile-menu');
                if (mobileMenu.classList.contains('open')) {
                    mobileMenu.classList.remove('open');
                }
            }
        });
    </script>

    @php
        $student_progress = $student_progress ?? [
            'beginner' => 65,
            'refresher' => 20,
            'commercial' => 10,
            'motorcycle' => 5,
        ];

        $monthly_enrollments = $monthly_enrollments ?? array_fill(0, 12, 0);
        $monthly_completions = $monthly_completions ?? array_fill(0, 12, 0);
    @endphp

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            // Student Progress Distribution
            const studentProgress = @json($student_progress);

            new Chart(document.getElementById("studentProgressChart"), {
                type: "doughnut",
                data: {
                    labels: ["Beginner", "Refresher", "Commercial", "Motorcycle"],
                    datasets: [{
                        data: [
                            studentProgress.beginner,
                            studentProgress.refresher,
                            studentProgress.commercial,
                            studentProgress.motorcycle
                        ],
                        backgroundColor: [
                            "rgb(37, 99, 235)",
                            "rgb(16, 185, 129)",
                            "rgb(245, 158, 11)",
                            "rgb(239, 68, 68)"
                        ]
                    }]
                }
            });

            // Monthly Enrollments vs Completions
            const monthlyLabels = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];

            const enrollmentData = @json($monthly_enrollments);
            const completionData = @json($monthly_completions);

            new Chart(document.getElementById("enrollmentChart"), {
                type: "line",
                data: {
                    labels: monthlyLabels,
                    datasets: [
                        {
                            label: "Enrollments",
                            data: enrollmentData,
                            borderColor: "rgb(37, 99, 235)",
                            backgroundColor: "rgba(37, 99, 235, 0.3)",
                            borderWidth: 2,
                            tension: 0.4
                        },
                        {
                            label: "Completions",
                            data: completionData,
                            borderColor: "rgb(16, 185, 129)",
                            backgroundColor: "rgba(16, 185, 129, 0.3)",
                            borderWidth: 2,
                            tension: 0.4
                        }
                    ]
                }
            });
        });
    </script>

    <!-- Livewire Scripts -->
    @livewireScripts
    <!-- Alpine.js -->
    <script src="//unpkg.com/alpinejs" defer></script>
    <!-- Additional Scripts -->
    @stack('scripts')
</body>
</html>
