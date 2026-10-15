<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo e($person->full_name); ?> - BioVerify AI</title>

    
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.4/css/dataTables.dataTables.min.css">

    
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/3.0.7/css/responsive.dataTables.min.css">

    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <style>
        /* Custom styles for the PDF viewer */
        .pdf-viewer {
            width: 100%;
            height: 600px;
            border: 1px solid #ccc;
            border-radius: 8px;
            overflow: hidden;
        }

        #pong-widget {
            position: fixed;
            right: 24px;
            bottom: 24px;
            z-index: 99999;
            pointer-events: auto;
        }

        #pong-toggle {
            position: relative;
            z-index: 100001;
            pointer-events: auto;
            cursor: pointer;
        }

        #pong-panel {
            position: absolute;
            right: 0;
            bottom: 76px;
            z-index: 100000;
            pointer-events: auto;
        }

        /* PCG sidebar layout */
        :root {
            --pcg-sidebar-width: 280px;
            --pcg-sidebar-collapsed-width: 78px;
        }

        .sidebar {
            position: fixed;
            inset: 0 auto 0 0;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            width: var(--pcg-sidebar-width);
            overflow: hidden;
            background: linear-gradient(180deg, #071b3a 0%, #0b2c59 100%);
            border-right: 1px solid rgba(255, 255, 255, .08);
            transition: width .25s ease, transform .25s ease;
        }

        .brand-mark {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            width: 42px;
            height: 42px;
            border: 1px solid rgba(255, 255, 255, .2);
            border-radius: 12px;
            background: #164e9c;
            color: #fff;
            font-size: 13px;
            font-weight: 900;
            letter-spacing: .08em;
        }

        .gold-line {
            height: 2px;
            flex-shrink: 0;
            background: linear-gradient(90deg, #d4a94d, #f6df9b, #d4a94d);
        }

        .section-label {
            color: #8faed7;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: .15em;
            text-transform: uppercase;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            min-height: 46px;
            padding: 11px 13px;
            border: 1px solid transparent;
            border-radius: 10px;
            color: #d7e5f8;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            transition: background .2s, color .2s, border-color .2s;
        }

        .nav-link:hover {
            color: #fff;
            background: rgba(255, 255, 255, .08);
        }

        .nav-link.active {
            color: #fff;
            background: rgba(37, 99, 235, .28);
            border-color: rgba(147, 197, 253, .2);
        }

        .nav-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 24px;
            flex-shrink: 0;
            font-size: 20px;
        }

        .service-card {
            border: 1px solid rgba(255, 255, 255, .1);
            background: rgba(255, 255, 255, .045);
        }

        .service-dot {
            width: 9px;
            height: 9px;
            flex-shrink: 0;
            border-radius: 50%;
            background: #34d399;
            box-shadow: 0 0 10px rgba(52, 211, 153, .4);
        }

        /* Main page shifts beside the sidebar */
        body {
            transition: padding-left .25s ease;
            padding-left: var(--pcg-sidebar-width);
        }

        body.sidebar-collapsed {
            padding-left: var(--pcg-sidebar-collapsed-width);
        }

        body.sidebar-collapsed .sidebar {
            width: var(--pcg-sidebar-collapsed-width);
        }

        body.sidebar-collapsed .sidebar-label,
        body.sidebar-collapsed .nav-badge {
            display: none;
        }

        body.sidebar-collapsed .sidebar-brand,
        body.sidebar-collapsed .sidebar-workspace,
        body.sidebar-collapsed .sidebar-footer {
            padding-left: 17px;
            padding-right: 17px;
        }

        body.sidebar-collapsed .nav-link {
            justify-content: center;
            padding-left: 8px;
            padding-right: 8px;
        }

        body.sidebar-collapsed .sidebar-toggle-icon {
            transform: rotate(180deg);
        }

        /* Keep the floating chat above the sidebar */
        #pong-widget {
            z-index: 1100;
        }

        /* Mobile layout */
        @media (max-width: 767px) {

            body,
            body.sidebar-collapsed {
                padding-left: 0;
            }

            .sidebar {
                width: min(280px, 85vw);
                transform: translateX(-100%);
            }

            body.sidebar-mobile-open .sidebar {
                transform: translateX(0);
            }

            body.sidebar-collapsed .sidebar {
                width: min(280px, 85vw);
            }

            body.sidebar-collapsed .sidebar-label,
            body.sidebar-collapsed .nav-badge {
                display: initial;
            }

            body.sidebar-collapsed .sidebar-brand,
            body.sidebar-collapsed .sidebar-workspace,
            body.sidebar-collapsed .sidebar-footer {
                padding-left: 20px;
                padding-right: 20px;
            }

            body.sidebar-collapsed .nav-link {
                justify-content: flex-start;
                padding: 11px 13px;
            }
        }
    </style>
</head>

<body class="min-h-screen bg-slate-50 text-slate-800">

    
    <aside id="pcgSidebar" class="sidebar text-white">

        <div class="sidebar-brand px-5 py-6">
            <div class="flex items-center gap-3">
                <div class="brand-mark">PCG</div>
                <div class="sidebar-label min-w-0">
                    <h1 class="whitespace-nowrap text-sm font-black tracking-[.13em]">
                        Pong Pagong
                    </h1>
                    <p class="mt-1 whitespace-nowrap text-xs text-blue-200">
                        Knowledge Assistant
                    </p>
                    <p class="mt-2 whitespace-nowrap text-[9px] font-bold uppercase tracking-[.13em] text-amber-300">
                        Personnel Support Workspace
                    </p>
                </div>
            </div>
        </div>

        <div class="gold-line"></div>

        <div class="sidebar-workspace flex items-center justify-between px-5 pb-2 pt-5">
            <p class="sidebar-label section-label">Workspace</p>

            <button id="sidebarToggle" type="button"
                class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border border-white/10 bg-white/5 text-blue-100 transition hover:bg-white/10 hover:text-white"
                aria-label="Collapse sidebar" aria-expanded="true" title="Collapse sidebar">
                <svg class="sidebar-toggle-icon h-4 w-4 transition-transform duration-300" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    aria-hidden="true">
                    <path d="m15 18-6-6 6-6" />
                </svg>
            </button>
        </div>

        <nav class="sidebar-nav flex-1 space-y-1 overflow-x-hidden overflow-y-auto px-3 pb-5">

            <a href="<?php echo e(route('ai-chat.index')); ?>" class="nav-link" title="Pong Pagong">
                <span class="nav-icon">✳</span>
                <span class="sidebar-label nav-text">Pong Pagong</span>
                <span class="nav-badge ml-auto rounded-md bg-white/10 px-2 py-1 text-[9px] font-bold text-amber-200">
                    AI
                </span>
            </a>

            <a href="<?php echo e(url('/pdf-documents')); ?>" class="nav-link">
                <span class="nav-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                        <path d="M14 2v6h6" />
                        <path d="M8 13h8" />
                        <path d="M8 17h8" />
                        <path d="M8 9h2" />
                    </svg>
                </span>

                <span class="nav-text">PDF Documents</span>
            </a>

            <a href="<?php echo e(route('biometric.login')); ?>" class="nav-link" title="Biometric Login">
                <span class="nav-icon">⌘</span>
                <span class="sidebar-label nav-text">Biometric Login</span>
            </a>

            <a href="<?php echo e(route('face.recognition.index')); ?>" class="nav-link" title="Face Recognition">
                <span class="nav-icon">◎</span>
                <span class="sidebar-label nav-text">Face Recognition</span>
            </a>

            <a href="<?php echo e(route('people.index')); ?>" class="nav-link active" aria-current="page" title="Personnel Records">
                <span class="nav-icon">♙</span>
                <span class="sidebar-label nav-text">Personnel Records</span>
            </a>

            <div class="sidebar-label px-3 pb-3 pt-9">
                <p class="section-label">Environment</p>
            </div>

            <div class="service-card mx-1 rounded-2xl p-4">
                <div class="flex items-center gap-3">
                    <span class="service-dot"></span>
                    <div class="sidebar-label min-w-0">
                        <p class="whitespace-nowrap text-xs font-bold text-white">
                            AI Workspace
                        </p>
                        <p class="mt-1 whitespace-nowrap text-[10px] text-blue-200">
                            Configured service
                        </p>
                    </div>
                </div>

                <p class="sidebar-label mt-4 text-[11px] leading-5 text-blue-100/70">
                    Pong Pagong uses your configured AI service and searchable knowledge base.
                </p>

                <div class="sidebar-label mt-4 border-t border-white/10 pt-3">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-amber-200">
                        Security reminder
                    </p>
                    <p class="mt-2 text-[11px] leading-5 text-blue-100/70">
                        Never submit passwords, classified material, or unnecessary personal and biometric data.
                    </p>
                </div>
            </div>
        </nav>

        <div class="sidebar-footer border-t border-white/10 px-5 py-4">
            <div class="flex items-center gap-2">
                <span
                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border border-white/10 bg-white/5 text-xs font-bold text-amber-200">
                    P
                </span>
                <div class="sidebar-label min-w-0">
                    <p class="whitespace-nowrap text-[11px] font-bold text-white">
                        Pong Pagong
                    </p>
                    <p class="mt-1 whitespace-nowrap text-[9px] text-blue-200/60">
                        Knowledge &amp; technical support
                    </p>
                </div>
            </div>
        </div>
    </aside>

    
    <nav class="sticky top-0 z-50 border-b border-slate-200 bg-white shadow-sm">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">

            
            <a href="<?php echo e(route('people.index')); ?>" class="flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-700 text-white shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 3l8 4v5c0 5-3.5 8-8 9-4.5-1-8-4-8-9V7l8-4z" />
                        <circle cx="12" cy="11" r="2.5" />
                        <path stroke-linecap="round" d="M8.5 16c.8-1.5 2-2.2 3.5-2.2s2.7.7 3.5 2.2" />
                    </svg>
                </div>

                <div>
                    <h1 class="text-lg font-extrabold tracking-tight text-slate-900 sm:text-xl">
                        BioVerify <span class="text-blue-700">AI</span>
                    </h1>
                    <p class="text-xs text-slate-500">Biometric Identity Management</p>
                </div>
            </a>

            
            <div class="flex items-center gap-2 sm:gap-3">

                <a href="<?php echo e(route('people.index')); ?>"
                    class="hidden items-center gap-2 rounded-lg px-3 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-100 hover:text-blue-700 sm:flex">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 10.5L12 3l9 7.5M5 9v11h14V9M9 20v-6h6v6" />
                    </svg>
                    People
                </a>

                
                <a href="<?php echo e(route('ai-chat.index')); ?>"
                    class="inline-flex items-center gap-2 rounded-lg bg-blue-50 px-3 py-2 text-sm font-semibold text-blue-700 transition hover:bg-blue-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M8 10h8M8 14h5m-8 6 3-3h8a4 4 0 004-4V7a4 4 0 00-4-4H8a4 4 0 00-4 4v6a4 4 0 001 2.6L5 20z" />
                    </svg>
                    <span class="hidden sm:inline">Ask Pong AI</span>
                    <span class="sm:hidden">AI</span>
                </a>

                <div class="hidden h-9 w-px bg-slate-200 sm:block"></div>

                <div class="hidden text-right sm:block">
                    <p class="text-sm font-semibold text-slate-800">
                        <?php echo e($person->full_name); ?>

                    </p>
                    <p class="text-xs text-slate-500">Person Profile</p>
                </div>

                
                <form action="<?php echo e(route('logout')); ?>" method="POST"
                    onsubmit="return confirm('Are you sure you want to log out?');">
                    <?php echo csrf_field(); ?>

                    <button type="submit"
                        class="inline-flex items-center gap-2 rounded-lg bg-red-50 px-3 py-2.5 text-sm font-semibold text-red-600 transition hover:bg-red-600 hover:text-white sm:px-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6A2.25 2.25 0 005.25 5.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M18 15l3-3m0 0l-3-3m3 3H9" />
                        </svg>
                        <span>Logout</span>
                    </button>
                </form>

            </div>
        </div>
    </nav>

    <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

        
        <div class="mb-8 flex flex-col justify-between gap-5 sm:flex-row sm:items-center">
            <div>
                <div class="mb-2 flex items-center gap-2 text-sm text-slate-500">
                    <a href="<?php echo e(route('people.index')); ?>" class="transition hover:text-blue-700">
                        People
                    </a>
                    <span>/</span>
                    <span class="text-slate-700">Person Profile</span>
                </div>

                <h2 class="text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">
                    <?php echo e($person->full_name); ?>

                </h2>

                <p class="mt-2 text-sm text-slate-500">
                    Manage personal information, biometric samples, and recognition activity.
                </p>
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="<?php echo e(route('people.index')); ?>"
                    class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 18l-6-6 6-6" />
                    </svg>
                    Back
                </a>

                <a href="<?php echo e(route('people.edit', $person)); ?>"
                    class="inline-flex items-center gap-2 rounded-xl bg-blue-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-800">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M16.862 4.487l2.651 2.651M5 19l4.5-1 10-10a1.875 1.875 0 00-2.65-2.65l-10 10L5 19z" />
                    </svg>
                    Edit Profile
                </a>
            </div>
        </div>

        
        <?php if(session('success')): ?>
            <div class="mb-6 flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800"
                role="status">
                <svg xmlns="http://www.w3.org/2000/svg" class="mt-0.5 h-5 w-5 shrink-0" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
                <p class="text-sm font-medium"><?php echo e(session('success')); ?></p>
            </div>
        <?php endif; ?>

        
        <section class="mb-8 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div class="flex items-center gap-3 border-b border-slate-100 px-5 py-5 sm:px-7">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-blue-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="1.8">
                        <circle cx="12" cy="8" r="4" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 21v-2a8 8 0 0116 0v2" />
                    </svg>
                </div>

                <div>
                    <h3 class="text-lg font-bold text-slate-900">Personal Information</h3>
                    <p class="text-sm text-slate-500">Basic identity and contact details</p>
                </div>

                <div class="ml-auto">
                    <?php if($person->status === 'Active'): ?>
                        <span
                            class="inline-flex items-center gap-2 rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-bold text-emerald-700">
                            <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                            Active
                        </span>
                    <?php else: ?>
                        <span
                            class="inline-flex items-center gap-2 rounded-full bg-red-50 px-3 py-1.5 text-xs font-bold text-red-700">
                            <span class="h-2 w-2 rounded-full bg-red-500"></span>
                            Inactive
                        </span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-x-8 gap-y-7 p-5 sm:grid-cols-2 sm:p-7 lg:grid-cols-3">
                <div>
                    <p class="mb-1.5 text-xs font-semibold uppercase tracking-wider text-slate-400">Person ID</p>
                    <p class="break-words font-bold text-slate-800"><?php echo e($person->person_id); ?></p>
                </div>

                <div>
                    <p class="mb-1.5 text-xs font-semibold uppercase tracking-wider text-slate-400">Full Name</p>
                    <p class="break-words font-bold text-slate-800"><?php echo e($person->full_name); ?></p>
                </div>

                <div>
                    <p class="mb-1.5 text-xs font-semibold uppercase tracking-wider text-slate-400">Email Address</p>
                    <p class="break-words font-medium text-slate-700"><?php echo e($person->email ?: 'N/A'); ?></p>
                </div>

                <div>
                    <p class="mb-1.5 text-xs font-semibold uppercase tracking-wider text-slate-400">Mobile Number</p>
                    <p class="font-medium text-slate-700"><?php echo e($person->mobile_number ?: 'N/A'); ?></p>
                </div>

                <div>
                    <p class="mb-1.5 text-xs font-semibold uppercase tracking-wider text-slate-400">Registration Date
                    </p>
                    <p class="font-medium text-slate-700">
                        <?php echo e($person->created_at?->format('F d, Y h:i A') ?? 'N/A'); ?>

                    </p>
                </div>

                <div>
                    <p class="mb-1.5 text-xs font-semibold uppercase tracking-wider text-slate-400">Account Status</p>
                    <p class="font-medium text-slate-700"><?php echo e($person->status ?: 'N/A'); ?></p>
                </div>
            </div>
        </section>

        
        <div class="mb-5 flex flex-col gap-1">
            <h3 class="text-xl font-extrabold text-slate-900">Biometric Verification</h3>
            <p class="text-sm text-slate-500">Registered biometric samples available for this person.</p>
        </div>

        <div class="mb-8 grid grid-cols-1 gap-6 lg:grid-cols-2">

            
            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="flex items-center justify-between gap-3 border-b border-slate-100 p-5 sm:p-6">
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                                <rect x="3" y="3" width="18" height="18" rx="4" />
                                <circle cx="9" cy="10" r="1" />
                                <circle cx="15" cy="10" r="1" />
                                <path stroke-linecap="round" d="M8 15c1 1.3 2.3 2 4 2s3-.7 4-2" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-900">Face Recognition</h4>
                            <p class="mt-1 text-xs text-slate-500">Registered face samples</p>
                        </div>
                    </div>

                    <span class="shrink-0 rounded-full bg-blue-50 px-3 py-1.5 text-xs font-bold text-blue-700">
                        <?php echo e($person->faces->count()); ?> Samples
                    </span>
                </div>

                <div class="p-5 sm:p-6">
                    <?php if($person->faces->count()): ?>
                        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3">
                            <?php $__currentLoopData = $person->faces; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $face): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div
                                    class="overflow-hidden rounded-xl border border-slate-200 bg-white p-2 transition hover:border-blue-300 hover:shadow-sm">
                                    <div class="aspect-square overflow-hidden rounded-lg bg-slate-100">
                                        <img src="<?php echo e(asset('storage/' . $face->image_path)); ?>"
                                            alt="Registered face sample" loading="lazy"
                                            class="h-full w-full object-cover">
                                    </div>

                                    <div class="mt-3 flex min-h-6 items-center justify-center">
                                        <?php if($face->is_primary): ?>
                                            <span
                                                class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-1 text-xs font-bold text-emerald-700">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5"
                                                    fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M10 2l2.2 4.5 5 .7-3.6 3.5.9 5-4.5-2.4-4.5 2.4.9-5L2.8 7.2l5-.7L10 2z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                                Primary
                                            </span>
                                        <?php else: ?>
                                            <span class="text-xs text-slate-400">Face Sample</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php else: ?>
                        <div
                            class="rounded-xl border border-dashed border-slate-300 bg-slate-50 px-4 py-10 text-center">
                            <div
                                class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-100 text-blue-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <rect x="3" y="3" width="18" height="18" rx="4" />
                                    <circle cx="9" cy="10" r="1" />
                                    <circle cx="15" cy="10" r="1" />
                                    <path stroke-linecap="round" d="M8 15c1 1.3 2.3 2 4 2s3-.7 4-2" />
                                </svg>
                            </div>
                            <p class="font-semibold text-slate-800">No Face Samples</p>
                            <p class="mx-auto mt-1 max-w-xs text-sm text-slate-500">
                                Register a face sample to add biometric data to this profile.
                            </p>
                        </div>
                    <?php endif; ?>

                    <div class="mt-5 border-t border-slate-100 pt-5">
                        <a href="<?php echo e(route('people.faces.create', $person)); ?>"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-blue-700 px-4 py-3 text-sm font-bold text-white transition hover:bg-blue-800">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m-7-7h14" />
                            </svg>
                            Add Face Sample
                        </a>
                    </div>
                </div>
            </section>

            
            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="flex items-center justify-between gap-3 border-b border-slate-100 p-5 sm:p-6">
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-purple-50 text-purple-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                                <rect x="9" y="2" width="6" height="12" rx="3" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M5 10v2a7 7 0 0014 0v-2M12 19v3m-4 0h8" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-900">Voice Recognition</h4>
                            <p class="mt-1 text-xs text-slate-500">Registered voice samples</p>
                        </div>
                    </div>

                    <span class="shrink-0 rounded-full bg-purple-50 px-3 py-1.5 text-xs font-bold text-purple-700">
                        <?php echo e($person->voices->count()); ?> Samples
                    </span>
                </div>

                <div class="p-5 sm:p-6">
                    <?php if($person->voices->count()): ?>
                        <div class="space-y-4">
                            <?php $__currentLoopData = $person->voices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $voice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="rounded-xl border border-slate-200 p-4 transition hover:border-purple-200">
                                    <div class="mb-4 flex items-center justify-between gap-3">
                                        <div class="flex min-w-0 items-center gap-3">
                                            <div
                                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-purple-50 text-purple-700">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                    stroke-width="1.8">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M12 3v18m-4-13v8m-4-5v2m12-5v8m4-5v2" />
                                                </svg>
                                            </div>
                                            <div class="min-w-0">
                                                <p class="font-bold text-slate-800">Voice Sample</p>
                                                <p class="mt-1 text-xs text-slate-500">
                                                    <?php echo e($voice->created_at?->format('M d, Y h:i A') ?? 'Date unavailable'); ?>

                                                </p>
                                            </div>
                                        </div>

                                        <?php if($voice->is_primary): ?>
                                            <span
                                                class="shrink-0 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-700">
                                                Primary
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <audio controls preload="none" class="w-full">
                                        <source src="<?php echo e(asset('storage/' . $voice->audio_path)); ?>">
                                        Your browser does not support audio playback.
                                    </audio>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php else: ?>
                        <div
                            class="rounded-xl border border-dashed border-slate-300 bg-slate-50 px-4 py-10 text-center">
                            <div
                                class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-purple-100 text-purple-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <rect x="9" y="2" width="6" height="12" rx="3" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M5 10v2a7 7 0 0014 0v-2M12 19v3m-4 0h8" />
                                </svg>
                            </div>
                            <p class="font-semibold text-slate-800">No Voice Samples</p>
                            <p class="mx-auto mt-1 max-w-xs text-sm text-slate-500">
                                No voice recordings have been registered for this person.
                            </p>
                        </div>
                    <?php endif; ?>

                    <div class="mt-5 border-t border-slate-100 pt-5">
                        <a href="<?php echo e(route('people.voices.create', $person)); ?>"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-blue-700 px-4 py-3 text-sm font-bold text-white transition hover:bg-blue-800">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <rect x="9" y="2" width="6" height="12" rx="3" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M5 10v2a7 7 0 0014 0v-2M12 19v3m-4 0h8" />
                            </svg>
                            Add Voice Sample
                        </a>
                    </div>
                </div>
            </section>

        </div>

        
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div
                class="flex flex-col gap-3 border-b border-slate-100 px-5 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-7">
                <div class="flex items-center gap-3">
                    <div
                        class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">Recognition History</h3>
                        <p class="mt-1 text-sm text-slate-500">Recent AI recognition activity</p>
                    </div>
                </div>

                <span
                    class="inline-flex w-fit items-center rounded-full bg-slate-100 px-3 py-1.5 text-xs font-bold text-slate-600">
                    <?php echo e($person->recognitionLogs->count()); ?> Records
                </span>
            </div>


            <div class="w-full">
                <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">

                    
                    <div class="border-b border-slate-200 px-4 py-4 sm:px-6">
                        <h3 class="text-lg font-bold text-slate-800">
                            Recognition History
                        </h3>
                        <p class="mt-1 text-sm text-slate-500">
                            View and track biometric recognition activity.
                        </p>
                    </div>

                    
                    <div class="overflow-x-auto p-3 sm:p-5">
                        <table id="recognitionHistoryTable" class="w-full min-w-[850px] text-left text-sm">

                            <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
                                <tr>
                                    <th class="whitespace-nowrap px-4 py-3 font-bold">Date</th>
                                    <th class="whitespace-nowrap px-4 py-3 font-bold">Type</th>
                                    <th class="whitespace-nowrap px-4 py-3 font-bold">Face Score</th>
                                    <th class="whitespace-nowrap px-4 py-3 font-bold">Voice Score</th>
                                    <th class="whitespace-nowrap px-4 py-3 font-bold">Combined Score</th>
                                    <th class="whitespace-nowrap px-4 py-3 font-bold">Result</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-slate-100">
                                <?php $__currentLoopData = $person->recognitionLogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="transition hover:bg-blue-50/50">
                                        <td class="whitespace-nowrap px-4 py-4 text-slate-600"
                                            data-order="<?php echo e($log->created_at?->timestamp ?? 0); ?>">
                                            <?php echo e($log->created_at?->format('M d, Y h:i A') ?? 'N/A'); ?>

                                        </td>

                                        <td class="whitespace-nowrap px-4 py-4">
                                            <span
                                                class="inline-flex rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">
                                                <?php echo e($log->recognition_type); ?>

                                            </span>
                                        </td>

                                        <td class="whitespace-nowrap px-4 py-4 font-medium text-slate-700">
                                            <?php echo e($log->face_score ?? '-'); ?>

                                        </td>

                                        <td class="whitespace-nowrap px-4 py-4 font-medium text-slate-700">
                                            <?php echo e($log->voice_score ?? '-'); ?>

                                        </td>

                                        <td class="whitespace-nowrap px-4 py-4 font-bold text-slate-800">
                                            <?php echo e($log->combined_score ?? '-'); ?>

                                        </td>

                                        <td class="whitespace-nowrap px-4 py-4">
                                            <?php if($log->result === 'Matched'): ?>
                                                <span
                                                    class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-bold text-emerald-700">
                                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                                    Matched
                                                </span>
                                            <?php elseif($log->result === 'Review Required'): ?>
                                                <span
                                                    class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-3 py-1.5 text-xs font-bold text-amber-700">
                                                    <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                                    Review Required
                                                </span>
                                            <?php else: ?>
                                                <span
                                                    class="inline-flex items-center gap-1.5 rounded-full bg-red-50 px-3 py-1.5 text-xs font-bold text-red-700">
                                                    <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>
                                                    Not Matched
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>

                        </table>
                    </div>
                </div>
            </div>


            <div class="border-t border-slate-100 bg-slate-50 px-5 py-3 sm:px-7">
                <p class="text-xs text-slate-500">
                    Recognition scores and results are displayed as recorded by the recognition system.
                </p>
            </div>
        </section>

        
        <footer class="mt-8 pb-4 text-center text-xs text-slate-400">
            BioVerify AI &copy; <?php echo e(date('Y')); ?>. Biometric Identity Management.
        </footer>

    </main>



    
    <div id="pong-widget" class="fixed bottom-5 right-5 z-[100]">

        
        <button type="button" id="pong-toggle" aria-label="Open Ask Pong AI" aria-expanded="false"
            class="flex h-16 w-16 items-center justify-center rounded-full
               bg-blue-700 text-white shadow-2xl ring-4 ring-blue-100
               transition duration-200 hover:scale-105 hover:bg-blue-800
               focus:outline-none focus:ring-4 focus:ring-blue-300">
            <svg id="pong-toggle-icon" xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" viewBox="0 0 24 24"
                fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                stroke-linejoin="round">
                <path d="M21 11.5a8.4 8.4 0 0 1-.9 3.8
                     8.5 8.5 0 0 1-7.6 4.7
                     8.4 8.4 0 0 1-3.8-.9L3 21l1.9-5.7
                     a8.4 8.4 0 0 1-.9-3.8
                     8.5 8.5 0 0 1 4.7-7.6
                     8.4 8.4 0 0 1 3.8-.9h.5
                     a8.5 8.5 0 0 1 8 8z" />
            </svg>
        </button>

        
        <section id="pong-panel" aria-label="Ask Pong AI chat" aria-hidden="true"
            class="absolute bottom-20 right-0 hidden
               flex-col overflow-hidden rounded-2xl
               border border-slate-200 bg-white
               shadow-2xl"
            style="width:min(380px, calc(100vw - 32px)); height:min(540px, calc(100dvh - 120px));">

            
            <div
                class="flex shrink-0 items-center justify-between
                    bg-gradient-to-r from-blue-800 to-blue-600
                    px-4 py-4 text-white">

                <div class="flex min-w-0 items-center gap-3">
                    <div
                        class="flex h-11 w-11 shrink-0 items-center
                            justify-center rounded-xl bg-white/15">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 8V4H8" />
                            <rect width="16" height="12" x="4" y="8" rx="2" />
                            <path d="M2 14h2m16 0h2M9 13h.01M15 13h.01M9 17h6" />
                        </svg>
                    </div>

                    <div class="min-w-0">
                        <h3 class="truncate text-base font-bold">
                            Ask Pong AI
                        </h3>
                        <p class="text-xs text-blue-100">
                            <span
                                class="mr-1 inline-block h-2 w-2
                                     rounded-full bg-green-400"></span>
                            AI assistant
                        </p>
                    </div>
                </div>

                <button type="button" id="pong-close" aria-label="Close chat"
                    class="ml-3 rounded-lg p-2 text-white/90
                       transition hover:bg-white/15 hover:text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round">
                        <path d="m18 6-12 12M6 6l12 12" />
                    </svg>
                </button>
            </div>

            
            <div class="shrink-0 border-b border-slate-100
                    bg-blue-50 px-4 py-3">
                <p class="text-sm leading-5 text-slate-700">
                    Hello! I'm Pong AI. Type your question or tap the
                    microphone to speak.
                </p>
            </div>

            
            <div id="pong-messages" role="log" aria-live="polite" aria-relevant="additions"
                class="flex-1 space-y-4 overflow-y-auto
                   bg-slate-50 p-4"></div>

            
            <div id="pong-status" role="status" aria-live="polite"
                class="hidden shrink-0 border-t border-slate-100
                   bg-white px-4 py-2 text-xs text-blue-700">
            </div>

            
            <form id="pong-form" class="shrink-0 border-t border-slate-200 bg-white p-3">
                <div class="flex items-end gap-2">

                    <textarea id="pong-input" name="message" rows="1" maxlength="4000" placeholder="Ask Pong AI..."
                        aria-label="Your message"
                        class="max-h-28 min-w-0 flex-1 resize-none
                           rounded-xl border border-slate-300
                           bg-white px-3 py-3 text-sm text-slate-900
                           outline-none placeholder:text-slate-400
                           focus:border-blue-600 focus:ring-2
                           focus:ring-blue-100"
                        required></textarea>

                    
                    <button type="button" id="pong-mic" title="Speak your message" aria-label="Start voice input"
                        aria-pressed="false"
                        class="flex h-11 w-11 shrink-0 items-center
                           justify-center rounded-xl bg-slate-100
                           text-slate-700 transition
                           hover:bg-blue-100 hover:text-blue-700
                           focus:outline-none focus:ring-2
                           focus:ring-blue-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="9" y="2" width="6" height="12" rx="3" />
                            <path d="M5 10a7 7 0 0 0 14 0" />
                            <path d="M12 19v3M8 22h8" />
                        </svg>
                    </button>

                    
                    <button type="submit" id="pong-send" aria-label="Send message" title="Send message"
                        class="flex h-11 w-11 shrink-0 items-center
                           justify-center rounded-xl bg-blue-700
                           text-white transition hover:bg-blue-800
                           disabled:cursor-not-allowed
                           disabled:opacity-50
                           focus:outline-none focus:ring-2
                           focus:ring-blue-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m22 2-7 20-4-9-9-4Z" />
                            <path d="M22 2 11 13" />
                        </svg>
                    </button>

                </div>

                <p class="mt-2 text-center text-[10px] text-slate-400">
                    Enter to send · Shift + Enter for a new line
                </p>
            </form>
        </section>
    </div>

    
    <script>
        (() => {
            'use strict';

            // Prevent duplicate initialization if this script is included twice.
            if (window.__pongFloatingChatInitialized) return;
            window.__pongFloatingChatInitialized = true;

            function initializePongChat() {
                const widget = document.getElementById('pong-widget');
                const toggle = document.getElementById('pong-toggle');
                const panel = document.getElementById('pong-panel');
                const close = document.getElementById('pong-close');
                const messages = document.getElementById('pong-messages');
                const form = document.getElementById('pong-form');
                const input = document.getElementById('pong-input');
                const send = document.getElementById('pong-send');
                const mic = document.getElementById('pong-mic');
                const status = document.getElementById('pong-status');
                const toggleIcon = document.getElementById('pong-toggle-icon');

                if (
                    !widget || !toggle || !panel || !close ||
                    !messages || !form || !input || !send ||
                    !mic || !status
                ) {
                    console.error(
                        'Ask Pong AI initialization failed: a required element is missing.'
                    );
                    return;
                }

                // Use the existing Laravel named route.
                const chatUrl = <?php echo json_encode(route('ai-chat.send'), 15, 512) ?>;
                const csrfToken =
                    document.querySelector('meta[name="csrf-token"]')?.content || '';

                let isSending = false;
                let recognition = null;
                let isListening = false;
                let finalTranscript = '';

                function setStatus(message, visible = true) {
                    status.textContent = message;
                    status.classList.toggle('hidden', !visible);
                }

                function openChat() {
                    panel.classList.remove('hidden');
                    panel.classList.add('flex');
                    panel.setAttribute('aria-hidden', 'false');
                    toggle.setAttribute('aria-expanded', 'true');

                    toggleIcon.innerHTML =
                        '<path d="m18 6-12 12M6 6l12 12"/>';

                    input.focus();
                }

                function closeChat() {
                    panel.classList.add('hidden');
                    panel.classList.remove('flex');
                    panel.setAttribute('aria-hidden', 'true');
                    toggle.setAttribute('aria-expanded', 'false');

                    toggleIcon.innerHTML =
                        '<path d="M21 11.5a8.4 8.4 0 0 1-.9 3.8 ' +
                        ' 8.5 8.5 0 0 1-7.6 4.7 ' +
                        ' 8.4 8.4 0 0 1-3.8-.9L3 21l1.9-5.7 ' +
                        ' a8.4 8.4 0 0 1-.9-3.8 ' +
                        ' 8.5 8.5 0 0 1 4.7-7.6 ' +
                        ' 8.4 8.4 0 0 1 3.8-.9h.5 ' +
                        ' a8.5 8.5 0 0 1 8 8z"/>';
                }

                toggle.addEventListener('click', () => {
                    if (panel.classList.contains('hidden')) {
                        openChat();
                    } else {
                        closeChat();
                    }
                });

                close.addEventListener('click', closeChat);

                // Build message bubbles safely with textContent.
                function addMessage(text, sender) {
                    const row = document.createElement('div');
                    row.className = sender === 'user' ?
                        'flex justify-end' :
                        'flex justify-start';

                    const bubble = document.createElement('div');
                    bubble.className = sender === 'user' ?
                        'max-w-[85%] whitespace-pre-wrap break-words rounded-2xl rounded-br-md bg-blue-700 px-4 py-3 text-sm leading-6 text-white shadow-sm' :
                        'max-w-[90%] whitespace-pre-wrap break-words rounded-2xl rounded-bl-md border border-slate-200 bg-white px-4 py-3 text-sm leading-6 text-slate-700 shadow-sm';

                    bubble.textContent = text;
                    row.appendChild(bubble);
                    messages.appendChild(row);
                    messages.scrollTop = messages.scrollHeight;

                    return row;
                }

                function addTypingIndicator() {
                    const row = document.createElement('div');
                    row.className = 'flex justify-start';

                    const bubble = document.createElement('div');
                    bubble.className =
                        'rounded-2xl rounded-bl-md border border-slate-200 ' +
                        'bg-white px-4 py-3 text-sm text-slate-500';

                    bubble.textContent = 'Pong AI is thinking...';
                    row.appendChild(bubble);
                    messages.appendChild(row);
                    messages.scrollTop = messages.scrollHeight;

                    return row;
                }

                function extractAnswer(data) {
                    if (typeof data === 'string') return data;

                    if (!data || typeof data !== 'object') return '';

                    const candidates = [
                        data.answer,
                        data.reply,
                        data.response,
                        data.message,
                        data.data?.answer,
                        data.data?.reply,
                        data.data?.response,
                        data.result?.answer
                    ];

                    for (const candidate of candidates) {
                        if (typeof candidate === 'string' && candidate.trim()) {
                            return candidate.trim();
                        }
                    }

                    return '';
                }

                async function sendMessage(message) {
                    const trimmedMessage = message.trim();

                    if (!trimmedMessage || isSending) return;

                    addMessage(trimmedMessage, 'user');
                    input.value = '';
                    input.style.height = 'auto';

                    isSending = true;
                    send.disabled = true;
                    setStatus('Connecting to Pong AI...');

                    const typing = addTypingIndicator();

                    try {
                        const response = await fetch(chatUrl, {
                            method: 'POST',
                            credentials: 'same-origin',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({
                                message: trimmedMessage
                            })
                        });

                        const rawResponse = await response.text();
                        let data;

                        try {
                            data = JSON.parse(rawResponse);
                        } catch {
                            throw new Error(
                                'The server returned an unexpected response. ' +
                                'Check your Laravel chat controller.'
                            );
                        }

                        if (!response.ok) {
                            throw new Error(
                                data.message ||
                                data.error ||
                                `Request failed (${response.status}).`
                            );
                        }

                        const answer = extractAnswer(data);

                        if (!answer) {
                            console.error('Unexpected Pong AI response:', data);
                            throw new Error(
                                'Pong AI returned no answer. Check the JSON ' +
                                'response in your Laravel chat controller.'
                            );
                        }

                        typing.remove();
                        addMessage(answer, 'assistant');
                        setStatus('', false);

                    } catch (error) {
                        typing.remove();
                        console.error('Pong AI chat error:', error);

                        addMessage(
                            'Sorry, I could not get a response. ' +
                            error.message,
                            'assistant'
                        );

                        setStatus(
                            'Unable to complete the request. Please try again.'
                        );
                    } finally {
                        isSending = false;
                        send.disabled = false;
                        input.focus();
                    }
                }

                form.addEventListener('submit', (event) => {
                    event.preventDefault();
                    sendMessage(input.value);
                });

                input.addEventListener('keydown', (event) => {
                    if (event.key === 'Enter' && !event.shiftKey) {
                        event.preventDefault();
                        form.requestSubmit();
                    }
                });

                input.addEventListener('input', () => {
                    input.style.height = 'auto';
                    input.style.height =
                        Math.min(input.scrollHeight, 112) + 'px';
                });

                // Voice input: speech is transcribed into the message field.
                const SpeechRecognition =
                    window.SpeechRecognition ||
                    window.webkitSpeechRecognition;

                function resetMicButton() {
                    isListening = false;
                    mic.setAttribute('aria-pressed', 'false');
                    mic.setAttribute('aria-label', 'Start voice input');
                    mic.title = 'Speak your message';

                    mic.classList.remove('bg-red-100', 'text-red-700');
                    mic.classList.add('bg-slate-100', 'text-slate-700');
                }

                if (!SpeechRecognition) {
                    mic.addEventListener('click', () => {
                        setStatus(
                            'Voice input is not supported here. Please use ' +
                            'Google Chrome or Microsoft Edge.'
                        );
                    });
                } else {
                    recognition = new SpeechRecognition();
                    recognition.lang = 'en-US';
                    recognition.continuous = false;
                    recognition.interimResults = true;
                    recognition.maxAlternatives = 1;

                    mic.addEventListener('click', () => {
                        if (isListening) {
                            recognition.stop();
                            return;
                        }

                        finalTranscript = '';

                        try {
                            recognition.start();
                        } catch (error) {
                            console.warn(
                                'Could not start speech recognition:', error
                            );
                            setStatus(
                                'Could not start the microphone. Please try again.'
                            );
                        }
                    });

                    recognition.onstart = () => {
                        isListening = true;
                        mic.setAttribute('aria-pressed', 'true');
                        mic.setAttribute('aria-label', 'Stop voice input');
                        mic.title = 'Listening... click to stop';

                        mic.classList.remove(
                            'bg-slate-100', 'text-slate-700'
                        );
                        mic.classList.add('bg-red-100', 'text-red-700');

                        setStatus('Listening... speak now.');
                    };

                    recognition.onresult = (event) => {
                        let interimTranscript = '';

                        for (
                            let i = event.resultIndex; i < event.results.length; i++
                        ) {
                            const transcript =
                                event.results[i][0].transcript;

                            if (event.results[i].isFinal) {
                                finalTranscript += transcript;
                            } else {
                                interimTranscript += transcript;
                            }
                        }

                        input.value =
                            (finalTranscript + ' ' + interimTranscript).trim();

                        input.dispatchEvent(
                            new Event('input', {
                                bubbles: true
                            })
                        );
                    };

                    recognition.onerror = (event) => {
                        console.warn(
                            'Speech recognition error:', event.error
                        );

                        if (event.error === 'not-allowed') {
                            setStatus(
                                'Microphone access was denied. Allow microphone ' +
                                'permission in your browser settings.'
                            );
                        } else if (event.error === 'no-speech') {
                            setStatus('No speech detected. Tap the microphone to try again.');
                        } else if (event.error !== 'aborted') {
                            setStatus(
                                'Voice input encountered an error. Please try again.'
                            );
                        }
                    };

                    recognition.onend = () => {
                        resetMicButton();

                        if (input.value.trim()) {
                            setStatus(
                                'Voice captured. Review your message, then press Send.'
                            );
                        } else {
                            setStatus('', false);
                        }
                    };
                }

                // Close with Escape.
                document.addEventListener('keydown', (event) => {
                    if (
                        event.key === 'Escape' &&
                        !panel.classList.contains('hidden')
                    ) {
                        closeChat();
                    }
                });
            }

            if (document.readyState === 'loading') {
                document.addEventListener(
                    'DOMContentLoaded',
                    initializePongChat, {
                        once: true
                    }
                );
            } else {
                initializePongChat();
            }
        })();
    </script>
    
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>

    <script src="https://cdn.datatables.net/responsive/3.0.7/js/dataTables.responsive.min.js"></script>

    <script>
        $(document).ready(function() {
            if ($.fn.dataTable.isDataTable('#recognitionHistoryTable')) {
                return;
            }

            $('#recognitionHistoryTable').DataTable({
                responsive: true,
                autoWidth: false,
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50],
                order: [
                    [0, 'desc']
                ],
                searching: true,
                ordering: true,
                paging: true,
                info: true,

                language: {
                    search: '',
                    searchPlaceholder: 'Search recognition history...',
                    lengthMenu: 'Show _MENU_ entries',
                    info: 'Showing _START_ to _END_ of _TOTAL_ records',
                    infoEmpty: 'No recognition records available',
                    zeroRecords: 'No matching recognition records found',
                    emptyTable: 'No recognition activity available',
                    paginate: {
                        first: 'First',
                        last: 'Last',
                        next: 'Next',
                        previous: 'Previous'
                    }
                },

                columnDefs: [{
                    targets: [1, 5],
                    orderable: true
                }]
            });
        });
    </script>
    <script>
        (() => {
            const sidebar = document.getElementById('pcgSidebar');
            const toggle = document.getElementById('sidebarToggle');

            if (!sidebar || !toggle || toggle.dataset.initialized === 'true') {
                return;
            }

            toggle.dataset.initialized = 'true';

            toggle.addEventListener('click', () => {
                if (window.innerWidth <= 767) {
                    const isOpen = document.body.classList.toggle(
                        'sidebar-mobile-open'
                    );

                    toggle.setAttribute('aria-expanded', String(isOpen));
                    toggle.setAttribute(
                        'aria-label',
                        isOpen ? 'Close sidebar' : 'Open sidebar'
                    );
                    toggle.title = isOpen ? 'Close sidebar' : 'Open sidebar';
                    return;
                }

                const isCollapsed = document.body.classList.toggle(
                    'sidebar-collapsed'
                );

                toggle.setAttribute('aria-expanded', String(!isCollapsed));
                toggle.setAttribute(
                    'aria-label',
                    isCollapsed ? 'Expand sidebar' : 'Collapse sidebar'
                );
                toggle.title = isCollapsed ? 'Expand sidebar' : 'Collapse sidebar';
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    document.body.classList.remove('sidebar-mobile-open');
                    toggle.setAttribute('aria-expanded', 'false');
                }
            });

            window.addEventListener('resize', () => {
                if (window.innerWidth > 767) {
                    document.body.classList.remove('sidebar-mobile-open');
                }
            });
        })();
    </script>
</body>

</html>
<?php /**PATH C:\xampp\htdocs\bioverify-ai\resources\views/people/show.blade.php ENDPATH**/ ?>