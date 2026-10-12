@extends('layouts.app')

@section('title', 'Cocinarte | La comida casera de personas como vos')
@section('meta_description', 'Descubrí cocineros independientes de tu zona y pedí sus platos caseros recién hechos. Comida real hecha en casa con entrega o retiro.')
@section('canonical', route('home'))
@section('og_title', 'Cocinarte — La comida casera de personas como vos')
@section('og_image', asset('assets/home/hero_cook.jpg'))

@push('styles')
    <style>
        /* Efectos de slider y scroll suave */
        .cook-slider {
            scroll-behavior: smooth;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
        }

        .cook-slider::-webkit-scrollbar {
            display: none;
        }

        .text-gradient {
            background: linear-gradient(135deg, #ea580c 0%, #e11d48 50%, #9333ea 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .btn-gradient {
            background: linear-gradient(135deg, #ea580c 0%, #db2777 50%, #9333ea 100%);
        }

        .btn-gradient:hover {
            background: linear-gradient(135deg, #c2410c 0%, #be185d 50%, #7e22ce 100%);
        }

        .glow-effect {
            box-shadow: 0 14px 35px -8px rgba(225, 29, 72, 0.35);
        }

        @keyframes marquee {
            0% {
                transform: translateX(0%);
            }

            100% {
                transform: translateX(-50%);
            }
        }

        .animate-marquee {
            display: flex;
            width: max-content;
            animation: marquee 28s linear infinite;
        }

        .animate-marquee:hover {
            animation-play-state: paused;
        }

        /* ============================================================ */
        /* 5.1 PROPUESTA DE VALOR PARA COCINEROS (prefijo vp-)          */
        /* ============================================================ */
        .vp-section {
            --vp-ease: cubic-bezier(.2, .8, .2, 1);
            position: relative;
            overflow: hidden;
            isolation: isolate;
            background: #140a0f;
            color: #fff;
            padding: 5rem 0;
        }

        @media (min-width: 1024px) {
            .vp-section {
                padding: 7rem 0;
            }
        }

        .vp-bg {
            position: absolute;
            inset: 0;
            z-index: -1;
            pointer-events: none;
        }

        .vp-orb {
            position: absolute;
            border-radius: 9999px;
            filter: blur(100px);
            opacity: .5;
            animation: vp-float 16s ease-in-out infinite alternate;
        }

        .vp-orb--1 {
            width: 520px;
            height: 520px;
            background: #ea580c;
            top: -200px;
            left: -140px;
        }

        .vp-orb--2 {
            width: 480px;
            height: 480px;
            background: #db2777;
            bottom: -220px;
            right: -100px;
            animation-delay: -5s;
        }

        .vp-orb--3 {
            width: 380px;
            height: 380px;
            background: #9333ea;
            top: 35%;
            left: 45%;
            opacity: .3;
            animation-delay: -9s;
        }

        .vp-grid-pattern {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255, 255, 255, .045) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, .045) 1px, transparent 1px);
            background-size: 56px 56px;
            -webkit-mask-image: radial-gradient(ellipse 70% 60% at 50% 35%, #000 25%, transparent 75%);
            mask-image: radial-gradient(ellipse 70% 60% at 50% 35%, #000 25%, transparent 75%);
        }

        @keyframes vp-float {
            0% {
                transform: translate(0, 0) scale(1);
            }

            100% {
                transform: translate(40px, 30px) scale(1.08);
            }
        }

        /* Encabezado */
        .vp-head {
            text-align: center;
            max-width: 50rem;
            margin: 0 auto;
        }

        .vp-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: .55rem;
            padding: .45rem 1rem;
            border-radius: 999px;
            background: rgba(255, 255, 255, .06);
            border: 1px solid rgba(255, 255, 255, .12);
            font-size: .72rem;
            font-weight: 800;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: #fed7aa;
            -webkit-backdrop-filter: blur(8px);
            backdrop-filter: blur(8px);
        }

        .vp-eyebrow-dot {
            width: .5rem;
            height: .5rem;
            border-radius: 999px;
            background: #fb923c;
            animation: vp-ping 2s infinite;
        }

        @keyframes vp-ping {
            0% {
                box-shadow: 0 0 0 0 rgba(251, 146, 60, .6);
            }

            70% {
                box-shadow: 0 0 0 10px rgba(251, 146, 60, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(251, 146, 60, 0);
            }
        }

        .vp-title {
            font-size: clamp(2.1rem, 5vw, 3.75rem);
            font-weight: 900;
            line-height: 1.04;
            letter-spacing: -.035em;
            margin-top: 1.4rem;
        }

        .vp-title-grad {
            background: linear-gradient(100deg, #fb923c 0%, #f43f5e 45%, #c084fc 100%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .vp-lead {
            color: rgba(255, 255, 255, .7);
            font-size: 1rem;
            line-height: 1.7;
            max-width: 40rem;
            margin: 1.25rem auto 0;
        }

        @media (min-width: 640px) {
            .vp-lead {
                font-size: 1.075rem;
            }
        }

        /* Bento grid */
        .vp-bento {
            display: grid;
            gap: 1rem;
            grid-template-columns: 1fr;
            margin-top: 3.5rem;
        }

        @media (min-width: 768px) {
            .vp-bento {
                grid-template-columns: repeat(2, 1fr);
                gap: 1.25rem;
            }

            .vp-card--platform,
            .vp-card--more {
                grid-column: span 2;
            }
        }

        @media (min-width: 1024px) {
            .vp-bento {
                grid-template-columns: repeat(6, 1fr);
            }

            .vp-card--platform {
                grid-column: span 4;
            }

            .vp-card--reach {
                grid-column: span 2;
            }

            .vp-card--growth,
            .vp-card--more {
                grid-column: span 3;
            }
        }

        .vp-card {
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            border-radius: 1.75rem;
            padding: 1.75rem;
            background: linear-gradient(180deg, rgba(255, 255, 255, .075), rgba(255, 255, 255, .025));
            border: 1px solid rgba(255, 255, 255, .09);
            -webkit-backdrop-filter: blur(14px);
            backdrop-filter: blur(14px);
            transition: transform .4s var(--vp-ease), border-color .4s, box-shadow .4s;
        }

        @media (min-width: 640px) {
            .vp-card {
                padding: 2.25rem;
            }
        }

        /* Spotlight que sigue al cursor */
        .vp-card::before {
            content: "";
            position: absolute;
            inset: 0;
            background: radial-gradient(440px circle at var(--mx, 50%) var(--my, 0%), rgba(251, 113, 133, .17), transparent 45%);
            opacity: 0;
            transition: opacity .4s;
            pointer-events: none;
        }

        .vp-card:hover {
            transform: translateY(-6px);
            border-color: rgba(251, 146, 60, .35);
            box-shadow: 0 30px 60px -20px rgba(219, 39, 119, .4);
        }

        .vp-card:hover::before {
            opacity: 1;
        }

        .vp-card>* {
            position: relative;
            z-index: 1;
        }

        .vp-card-head {
            display: flex;
            align-items: center;
            gap: .9rem;
            margin-bottom: 1.25rem;
        }

        .vp-icon {
            width: 3rem;
            height: 3rem;
            flex-shrink: 0;
            border-radius: 1rem;
            display: grid;
            place-items: center;
            background: linear-gradient(135deg, #ea580c, #db2777 55%, #9333ea);
            box-shadow: 0 10px 25px -8px rgba(219, 39, 119, .65), inset 0 1px 0 rgba(255, 255, 255, .3);
            transition: transform .4s var(--vp-ease);
        }

        .vp-card:hover .vp-icon {
            transform: rotate(-6deg) scale(1.08);
        }

        .vp-icon svg {
            width: 1.5rem;
            height: 1.5rem;
            color: #fff;
        }

        .vp-kicker {
            font-size: .7rem;
            font-weight: 800;
            letter-spacing: .14em;
            text-transform: uppercase;
            color: #fdba74;
        }

        .vp-card h3 {
            font-size: 1.375rem;
            font-weight: 800;
            line-height: 1.2;
            letter-spacing: -.015em;
            color: #fff;
        }

        @media (min-width: 1024px) {
            .vp-card--platform h3 {
                font-size: 1.85rem;
            }
        }

        .vp-text {
            color: rgba(255, 255, 255, .66);
            font-size: .9rem;
            line-height: 1.65;
            margin-top: .75rem;
        }

        /* Card 1: Plataforma */
        .vp-platform {
            display: grid;
            gap: 2rem;
            align-items: center;
            height: 100%;
        }

        @media (min-width: 768px) {
            .vp-platform {
                grid-template-columns: 1.15fr 1fr;
            }
        }

        .vp-checks {
            display: flex;
            flex-wrap: wrap;
            gap: .5rem;
            margin-top: 1.4rem;
            list-style: none;
            padding: 0;
        }

        .vp-checks li {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            font-size: .78rem;
            font-weight: 600;
            color: rgba(255, 255, 255, .88);
            padding: .42rem .8rem;
            border-radius: 999px;
            background: rgba(255, 255, 255, .06);
            border: 1px solid rgba(255, 255, 255, .1);
        }

        .vp-checks svg {
            width: .9rem;
            height: .9rem;
            color: #4ade80;
        }

        .vp-mock {
            position: relative;
            display: flex;
            flex-direction: column;
            gap: .75rem;
        }

        .vp-notif {
            display: flex;
            align-items: center;
            gap: .8rem;
            padding: .85rem 1rem;
            border-radius: 1.1rem;
            box-shadow: 0 20px 40px -12px rgba(0, 0, 0, .55);
            animation: vp-bob 6s ease-in-out infinite;
        }

        .vp-notif--main {
            background: rgba(255, 255, 255, .97);
            color: #1f2937;
        }

        .vp-notif--glass {
            width: 86%;
            background: rgba(255, 255, 255, .09);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, .14);
            -webkit-backdrop-filter: blur(10px);
            backdrop-filter: blur(10px);
        }

        .vp-notif--right {
            margin-left: auto;
            animation-delay: -2s;
        }

        .vp-notif--left {
            animation-delay: -4s;
        }

        .vp-notif img {
            width: 2.9rem;
            height: 2.9rem;
            border-radius: .8rem;
            object-fit: cover;
            flex-shrink: 0;
        }

        .vp-notif-ico {
            width: 2.25rem;
            height: 2.25rem;
            border-radius: .7rem;
            display: grid;
            place-items: center;
            flex-shrink: 0;
        }

        .vp-notif-ico svg {
            width: 1.1rem;
            height: 1.1rem;
        }

        .vp-notif-label {
            font-size: .64rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .1em;
            color: #e11d48;
        }

        .vp-notif--glass .vp-notif-label {
            color: rgba(255, 255, 255, .55);
        }

        .vp-notif-body {
            font-size: .86rem;
            font-weight: 700;
            line-height: 1.3;
        }

        .vp-notif-end {
            margin-left: auto;
            text-align: right;
            white-space: nowrap;
        }

        .vp-notif-price {
            font-weight: 900;
            font-size: .95rem;
        }

        .vp-pill-ok {
            display: inline-flex;
            align-items: center;
            gap: .25rem;
            margin-top: .2rem;
            font-size: .62rem;
            font-weight: 800;
            color: #15803d;
            background: #dcfce7;
            padding: .15rem .5rem;
            border-radius: 999px;
        }

        @keyframes vp-bob {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-7px);
            }
        }

        /* Card 2: Difusión (radar) */
        .vp-radar {
            position: relative;
            height: 190px;
            margin-top: auto;
            padding-top: 1.5rem;
            display: grid;
            place-items: center;
        }

        .vp-radar-ring {
            position: absolute;
            width: 70px;
            height: 70px;
            border-radius: 999px;
            border: 1.5px solid rgba(251, 113, 133, .55);
            animation: vp-radar 3.6s cubic-bezier(.2, .6, .3, 1) infinite;
        }

        .vp-radar-ring:nth-child(2) {
            animation-delay: 1.2s;
        }

        .vp-radar-ring:nth-child(3) {
            animation-delay: 2.4s;
        }

        @keyframes vp-radar {
            0% {
                transform: scale(.6);
                opacity: .9;
            }

            100% {
                transform: scale(3.3);
                opacity: 0;
            }
        }

        .vp-radar-core {
            position: relative;
            width: 58px;
            height: 58px;
            border-radius: 999px;
            display: grid;
            place-items: center;
            background: linear-gradient(135deg, #ea580c, #db2777);
            box-shadow: 0 0 45px rgba(244, 63, 94, .65);
        }

        .vp-radar-core svg {
            width: 1.6rem;
            height: 1.6rem;
            color: #fff;
        }

        .vp-avatar {
            position: absolute;
            width: 36px;
            height: 36px;
            border-radius: 999px;
            display: grid;
            place-items: center;
            font-size: .7rem;
            font-weight: 800;
            color: #fff;
            border: 2px solid #1d1016;
            box-shadow: 0 8px 18px -6px rgba(0, 0, 0, .6);
            animation: vp-pop 4s ease-in-out infinite;
        }

        .vp-avatar--1 {
            top: 16%;
            left: 14%;
            background: linear-gradient(135deg, #f97316, #f43f5e);
        }

        .vp-avatar--2 {
            top: 20%;
            right: 12%;
            background: linear-gradient(135deg, #a855f7, #ec4899);
            animation-delay: -1s;
        }

        .vp-avatar--3 {
            bottom: 4%;
            left: 22%;
            background: linear-gradient(135deg, #f43f5e, #9333ea);
            animation-delay: -2s;
        }

        .vp-avatar--4 {
            bottom: 10%;
            right: 20%;
            background: linear-gradient(135deg, #fb923c, #db2777);
            animation-delay: -3s;
        }

        .vp-avatar--5 {
            top: 52%;
            left: 2%;
            width: 28px;
            height: 28px;
            font-size: .62rem;
            background: rgba(255, 255, 255, .15);
            animation-delay: -1.5s;
        }

        @keyframes vp-pop {

            0%,
            100% {
                transform: scale(1) translateY(0);
            }

            50% {
                transform: scale(1.1) translateY(-4px);
            }
        }

        /* Card 3: Crecimiento (gráfico) */
        .vp-chart {
            margin-top: auto;
            padding-top: 1.75rem;
        }

        .vp-chart svg {
            display: block;
            width: 100%;
            height: auto;
            overflow: visible;
        }

        .vp-chart-dot-ring {
            transform-box: fill-box;
            transform-origin: center;
            animation: vp-dot 2.2s ease-out infinite;
        }

        @keyframes vp-dot {
            0% {
                transform: scale(1);
                opacity: .8;
            }

            100% {
                transform: scale(3);
                opacity: 0;
            }
        }

        .vp-steps {
            display: flex;
            justify-content: space-between;
            gap: .5rem;
            margin-top: .9rem;
            font-size: .72rem;
            font-weight: 700;
            color: rgba(255, 255, 255, .5);
        }

        .vp-steps span:last-child {
            color: #fda4af;
        }

        /* Card 4: Y mucho más (marquee de chips) */
        .vp-marquee {
            position: relative;
            overflow: hidden;
            margin: auto -1.75rem 0;
            padding-top: 1.75rem;
            -webkit-mask-image: linear-gradient(90deg, transparent, #000 12%, #000 88%, transparent);
            mask-image: linear-gradient(90deg, transparent, #000 12%, #000 88%, transparent);
        }

        @media (min-width: 640px) {
            .vp-marquee {
                margin-left: -2.25rem;
                margin-right: -2.25rem;
            }
        }

        .vp-marquee-track {
            display: flex;
            width: max-content;
            animation: marquee 34s linear infinite;
        }

        .vp-marquee-track--rev {
            animation-direction: reverse;
            animation-duration: 40s;
            margin-top: .65rem;
        }

        .vp-marquee:hover .vp-marquee-track {
            animation-play-state: paused;
        }

        .vp-chip {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            margin-right: .65rem;
            white-space: nowrap;
            padding: .6rem 1.05rem;
            border-radius: 999px;
            background: rgba(255, 255, 255, .06);
            border: 1px solid rgba(255, 255, 255, .12);
            font-size: .8rem;
            font-weight: 600;
            color: rgba(255, 255, 255, .88);
        }

        .vp-chip::before {
            content: "";
            width: .45rem;
            height: .45rem;
            border-radius: 999px;
            background: linear-gradient(135deg, #fb923c, #f43f5e, #c084fc);
        }

        /* CTA final */
        .vp-cta {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1rem;
            margin-top: 3.5rem;
            text-align: center;
        }

        .vp-btn {
            position: relative;
            overflow: hidden;
            display: inline-flex;
            align-items: center;
            gap: .6rem;
            padding: 1.05rem 2.1rem;
            border-radius: 999px;
            font-weight: 800;
            font-size: 1rem;
            color: #fff;
            background: linear-gradient(135deg, #ea580c, #db2777 50%, #9333ea);
            box-shadow: 0 18px 40px -10px rgba(219, 39, 119, .65);
            transition: transform .25s var(--vp-ease), box-shadow .25s;
        }

        .vp-btn:hover {
            transform: translateY(-2px) scale(1.03);
            box-shadow: 0 24px 50px -10px rgba(219, 39, 119, .85);
        }

        .vp-btn:focus-visible {
            outline: 3px solid #fdba74;
            outline-offset: 3px;
        }

        .vp-btn::after {
            content: "";
            position: absolute;
            top: 0;
            left: -60%;
            width: 40%;
            height: 100%;
            background: linear-gradient(100deg, transparent, rgba(255, 255, 255, .45), transparent);
            transform: skewX(-20deg);
            animation: vp-shine 3.8s ease-in-out infinite;
        }

        @keyframes vp-shine {

            0%,
            60% {
                left: -60%;
            }

            100% {
                left: 130%;
            }
        }

        .vp-btn svg {
            width: 1.1rem;
            height: 1.1rem;
            transition: transform .25s;
        }

        .vp-btn:hover svg {
            transform: translateX(4px);
        }

        .vp-cta-note {
            font-size: .82rem;
            color: rgba(255, 255, 255, .55);
        }

        /* Reveal on scroll (solo si JS está activo: .vp-ready) */
        .vp-ready .vp-reveal {
            opacity: 0;
            translate: 0 28px;
            transition: opacity .8s var(--vp-ease) var(--d, 0ms), translate .8s var(--vp-ease) var(--d, 0ms);
        }

        .vp-ready .vp-card.vp-reveal {
            transition: opacity .8s var(--vp-ease) var(--d, 0ms), translate .8s var(--vp-ease) var(--d, 0ms),
                transform .4s var(--vp-ease), border-color .4s, box-shadow .4s;
        }

        .vp-ready .vp-reveal.is-visible {
            opacity: 1;
            translate: 0 0;
        }

        .vp-ready .vp-chart-line {
            stroke-dasharray: 1;
            stroke-dashoffset: 1;
            transition: stroke-dashoffset 2.2s cubic-bezier(.65, 0, .35, 1) .5s;
        }

        .vp-ready .vp-chart-area {
            opacity: 0;
            transition: opacity 1.2s ease 1.6s;
        }

        .vp-ready .is-visible .vp-chart-line {
            stroke-dashoffset: 0;
        }

        .vp-ready .is-visible .vp-chart-area {
            opacity: 1;
        }

        @media (prefers-reduced-motion: reduce) {

            .vp-section *,
            .vp-section *::before,
            .vp-section *::after {
                animation: none !important;
                transition: none !important;
            }

            .vp-ready .vp-reveal {
                opacity: 1;
                translate: 0 0;
            }

            .vp-ready .vp-chart-line {
                stroke-dashoffset: 0;
            }

            .vp-ready .vp-chart-area {
                opacity: 1;
            }
        }
    </style>
@endpush

@section('content')

    @php
        // Cocineros destacados de la maqueta (con soporte para incorporar cocineros reales de la BD)
        $featuredCooks = [
            [
                'id' => 1,
                'name' => 'Doña Rosa',
                'specialty' => 'Pastas Frescas',
                'location' => 'Córdoba Capital · 3 km',
                'rating' => '4.9',
                'reviews_count' => 124,
                'photo' => asset('assets/home/cook_dona_rosa.jpg'),
                'dishes' => [
                    asset('assets/home/dish_mini_lasagna.jpg'),
                    asset('assets/home/dish_mini_gnocchi.jpg'),
                    asset('assets/home/dish_mini_roast.jpg'),
                ],
                'url' => route('marketplace.catalog', ['search' => 'pastas'])
            ],
            [
                'id' => 2,
                'name' => 'Leo Esquivel',
                'specialty' => 'Comida Italiana',
                'location' => 'Villa Allende · 7 km',
                'rating' => '4.8',
                'reviews_count' => 96,
                'photo' => asset('assets/home/cook_leo.jpg'),
                'dishes' => [
                    asset('assets/front/pasta_casera.png'),
                    asset('assets/home/dish_mini_lasagna.jpg'),
                    asset('assets/home/dish_mini_gnocchi.jpg'),
                ],
                'url' => route('marketplace.catalog', ['search' => 'italiana'])
            ],
            [
                'id' => 3,
                'name' => 'Mariana López',
                'specialty' => 'Cocina Tradicional',
                'location' => 'Córdoba Capital · 4 km',
                'rating' => '4.9',
                'reviews_count' => 87,
                'photo' => asset('assets/home/cook_mariana.jpg'),
                'dishes' => [
                    asset('assets/home/dish_mini_gnocchi.jpg'),
                    asset('assets/home/dish_mini_roast.jpg'),
                    asset('assets/front/pollo_curry_casero.png'),
                ],
                'url' => route('marketplace.catalog', ['search' => 'tradicional'])
            ],
            [
                'id' => 4,
                'name' => 'Fernando Catalano',
                'specialty' => 'Cocina de Autor',
                'location' => 'Bell Ville · 6 km',
                'rating' => '4.8',
                'reviews_count' => 72,
                'photo' => asset('assets/home/cook_fernando.jpg'),
                'dishes' => [
                    asset('assets/home/dish_mini_roast.jpg'),
                    asset('assets/front/ensalada_casera.png'),
                    asset('assets/front/torta_casera.png'),
                ],
                'url' => route('marketplace.catalog', ['search' => 'autor'])
            ],
            [
                'id' => 5,
                'name' => 'Paula Nieva',
                'specialty' => 'Empanadas y Tartas Caseras',
                'location' => 'Bell Ville · 3 km',
                'rating' => '4.6',
                'reviews_count' => 64,
                'photo' => asset('assets/home/cook_paula.jpg'),
                'dishes' => [
                    asset('assets/home/dish_mini_empanadas.jpg'),
                    asset('assets/home/dish_mini_milanesa.jpg'),
                    asset('assets/home/dish_mini_tart.jpg'),
                ],
                'url' => route('marketplace.catalog', ['search' => 'empanadas'])
            ],
            [
                'id' => 6,
                'name' => 'Soledad Pérez',
                'specialty' => 'Guisos y Postres Caseros',
                'location' => 'Bell Ville · 2 km',
                'rating' => '4.7',
                'reviews_count' => 58,
                'photo' => asset('assets/home/cook_soledad.jpg'),
                'dishes' => [
                    asset('assets/home/dish_mini_stew.jpg'),
                    asset('assets/home/dish_mini_flan.jpg'),
                    asset('assets/home/dish_mini_roast.jpg'),
                ],
                'url' => route('marketplace.catalog', ['search' => 'guisos'])
            ],
        ];

        // Si existen cocineros reales aprobados en la BD, enriquecemos la lista
        if (isset($dbCooks) && $dbCooks->isNotEmpty()) {
            $realList = [];
            foreach ($dbCooks as $realCook) {
                $dishPhotos = [];
                foreach ($realCook->dishes as $d) {
                    if (!empty($d->image_path)) {
                        $dishPhotos[] = asset('storage/' . $d->image_path);
                    }
                }
                while (count($dishPhotos) < 3) {
                    $dishPhotos[] = asset('assets/home/dish_mini_lasagna.jpg');
                }

                $cookPhoto = asset('assets/home/cook_mariana.jpg');
                if (!empty($realCook->profile_photo_path)) {
                    $cookPhoto = asset('storage/' . $realCook->profile_photo_path);
                } elseif (!empty($realCook->user->profile_photo_path)) {
                    $cookPhoto = asset('uploads/' . $realCook->user->profile_photo_path);
                }

                $realList[] = [
                    'id' => $realCook->id,
                    'name' => $realCook->user->name ?? 'Cocinero Cocinarte',
                    'specialty' => $realCook->bio_specialty ?? 'Cocina Casera',
                    'location' => ($realCook->city ?? 'Tu zona') . ' · ' . ($realCook->neighborhood ?? 'Cerca tuyo'),
                    'rating' => number_format($realCook->reviews_avg_rating ?? 4.9, 1),
                    'reviews_count' => $realCook->reviews_count ?? 15,
                    'photo' => $cookPhoto,
                    'dishes' => array_slice($dishPhotos, 0, 3),
                    'url' => route('marketplace.cook.profile', $realCook->id)
                ];
            }
            if (!empty($realList)) {
                $featuredCooks = array_merge($realList, $featuredCooks);
            }
        }
    @endphp

    <div class="bg-[#faf8f5] text-gray-700 antialiased overflow-hidden">

        <!-- ========================================== -->
        <!-- 1. HERO SECTION (FULLWIDTH & RESPONSIVE)    -->
        <!-- ========================================== -->

        <!-- VERSIÓN DESKTOP / TABLET (Fullwidth con degradé horizontal hacia la imagen) -->
        <section
            class="hidden md:flex relative w-full overflow-hidden bg-[#faf8f5] min-h-[500px] lg:min-h-[560px] xl:min-h-[600px] flex items-center border-b border-orange-100/30">
            <!-- Imagen Fullwidth a la derecha -->
            <div class="absolute inset-y-0 right-0 w-full md:w-[68%] lg:w-[64%] xl:w-[60%] h-full z-0">
                <img src="{{ asset('assets/home/hero_cook.jpg') }}" alt="Cocinera preparando comida casera en Cocinarte"
                    class="w-full h-full object-cover object-[center_right] lg:object-center" fetchpriority="high">
                <!-- Fusión con degradé horizontal hacia la izquierda -->
                <div
                    class="absolute inset-y-0 left-0 w-44 lg:w-72 bg-gradient-to-r from-[#faf8f5] via-[#faf8f5]/85 to-transparent pointer-events-none">
                </div>
            </div>

            <!-- Overlay de fondo para asegurar legibilidad perfecta de los textos a la izquierda -->
            <div
                class="absolute inset-y-0 left-0 w-full md:w-[58%] lg:w-[52%] xl:w-[48%] bg-gradient-to-r from-[#faf8f5] via-[#faf8f5] to-transparent z-10 pointer-events-none">
            </div>

            <!-- Contenido textual alineado a la izquierda dentro del contenedor -->
            <div class="container flex items-center mx-auto px-4 sm:px-6 lg:px-8 relative z-20 py-12 lg:py-16">
                <div class="max-w-xl space-y-6 lg:space-y-7">
                    <h1
                        class="text-4xl lg:text-5xl xl:text-[3.4rem] font-black tracking-tight text-gray-700 leading-[1.12]">
                        <span class="text-gradient block">
                            La comida casera
                        </span>
                        <span class="text-gray-700 block mt-1">
                            de personas como vos.
                        </span>
                    </h1>

                    <p class="text-base lg:text-lg text-gray-600 leading-relaxed font-normal max-w-md">
                        Descubrí cocineros independientes de tu zona y pedí sus platos hechos en casa.
                    </p>

                    <!-- Botones Principales -->
                    <div class="flex items-center gap-4 pt-1">
                        <a href="{{ route('marketplace.catalog') }}"
                            class="group btn-gradient text-white px-7 py-3.5 rounded-full font-bold text-sm lg:text-base shadow-md glow-effect hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200 inline-flex items-center justify-center whitespace-nowrap">
                            <span>Explorar cocineros</span>
                            <svg class="w-4 h-4 ml-2.5 transition-transform duration-200 group-hover:translate-x-1"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </a>

                        <a href="{{ route('register', ['role' => 'cook']) }}"
                            class="px-7 py-3.5 rounded-full font-bold text-sm lg:text-base bg-white hover:bg-rose-50/50 text-purple-950 border border-purple-300 shadow-sm hover:shadow transition-all duration-200 inline-flex items-center justify-center whitespace-nowrap">
                            Quiero cocinar
                        </a>
                    </div>

                    <!-- Micro-Badges de Confianza con Íconos -->
                    <div class="pt-2 flex items-center space-x-3 text-xs lg:text-sm font-medium text-gray-600">
                        <div class="inline-flex items-center space-x-1.5">
                            <span class="text-rose-500 font-bold">♡</span>
                            <span>Cocineros reales</span>
                        </div>
                        <span class="text-gray-300 font-bold">·</span>
                        <div class="inline-flex items-center space-x-1.5">
                            <span>Cocinas reales</span>
                        </div>
                        <span class="text-gray-300 font-bold">·</span>
                        <div class="inline-flex items-center space-x-1.5">
                            <span>Comida hecha en casa</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- VERSIÓN MOBILE (Títulos y botones más chicos en una sola línea arriba, y debajo degradé a la imagen) -->
        <section class="md:hidden relative w-full bg-[#faf8f5] pt-6 pb-2 overflow-hidden">
            <!-- Bloque superior de contenidos -->
            <div class="px-5 space-y-4 text-left">
                <h1 class="text-3xl font-black tracking-tight leading-[1.15]">
                    <span class="text-gradient block">
                        La comida casera
                    </span>
                    <span class="text-gray-950 block mt-0.5">
                        de personas como vos.
                    </span>
                </h1>

                <p class="text-xs sm:text-sm text-gray-600 leading-relaxed font-normal">
                    Descubrí cocineros independientes de tu zona y pedí sus platos hechos en casa.
                </p>

                <!-- Botones más chicos en una sola línea horizontal -->
                <div class="flex items-center gap-2 pt-1 w-full max-w-sm">
                    <a href="{{ route('marketplace.catalog') }}"
                        class="flex-1 btn-gradient text-white py-2.5 px-2.5 rounded-full font-bold text-[11px] sm:text-xs shadow-sm inline-flex items-center justify-center text-center whitespace-nowrap">
                        <span>Explorar cocineros</span>
                        <span class="ml-1 text-[11px]">→</span>
                    </a>

                    <a href="{{ route('register', ['role' => 'cook']) }}"
                        class="flex-1 bg-white hover:bg-rose-50/50 text-purple-950 border border-purple-300 py-2.5 px-2.5 rounded-full font-bold text-[11px] sm:text-xs shadow-sm inline-flex items-center justify-center text-center whitespace-nowrap">
                        Quiero cocinar
                    </a>
                </div>

                <!-- Badges de Confianza compactos -->
                <div class="pt-1 flex flex-wrap items-center gap-x-2 gap-y-1 text-[11px] font-medium text-gray-500">
                    <div class="inline-flex items-center space-x-1">
                        <span class="text-rose-500 font-bold">♡</span>
                        <span>Cocineros reales</span>
                    </div>
                    <span class="text-gray-300">·</span>
                    <span>Cocinas reales</span>
                    <span class="text-gray-300">·</span>
                    <span>Comida hecha en casa</span>
                </div>
            </div>

            <!-- Imagen debajo con degradé superior e inferior -->
            <div class="relative w-full h-72 sm:h-80 mt-5 overflow-hidden">
                <img src="{{ asset('assets/home/hero_cook.jpg') }}" alt="Cocinera preparando comida casera en Cocinarte"
                    class="w-full h-full object-cover object-[center_right]" loading="eager">

                <!-- Degradé superior que funde la imagen con el contenido de arriba -->
                <div
                    class="absolute inset-x-0 top-0 h-20 bg-gradient-to-b from-[#faf8f5] via-[#faf8f5]/70 to-transparent pointer-events-none">
                </div>

                <!-- Degradé inferior hacia la siguiente sección -->
                <div
                    class="absolute inset-x-0 bottom-0 h-10 bg-gradient-to-t from-[#faf8f5] to-transparent pointer-events-none">
                </div>
            </div>
        </section>


        <!-- ============================================================ -->
        <!-- BANNER MARQUEE DE ALTO IMPACTO (MOVIMIENTO CONTINUO)         -->
        <!-- ============================================================ -->
        <section
            class="relative w-full overflow-hidden bg-gradient-to-r from-orange-500 via-rose-500 to-purple-600 text-white py-3.5 sm:py-4 shadow-lg shadow-rose-500/15 border-y border-white/20 select-none">
            <!-- Máscaras de degradé en los extremos para desvanecimiento elegante -->
            <div
                class="pointer-events-none absolute inset-y-0 left-0 w-10 sm:w-24 bg-gradient-to-r from-orange-500 via-orange-500/80 to-transparent z-10">
            </div>
            <div
                class="pointer-events-none absolute inset-y-0 right-0 w-10 sm:w-24 bg-gradient-to-l from-purple-600 via-purple-600/80 to-transparent z-10">
            </div>

            <div class="animate-marquee flex items-center gap-8 sm:gap-14 whitespace-nowrap">
                <!-- Bloque 1 de items -->
                <div class="flex items-center gap-8 sm:gap-14 shrink-0">
                    <a href="{{ route('register', ['role' => 'cook']) }}"
                        class="inline-flex items-center gap-3 group hover:opacity-95 transition-opacity">
                        <span
                            class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-white/20 backdrop-blur-md shadow-inner text-amber-200 text-sm group-hover:scale-110 transition-transform">✨</span>
                        <span class="font-black text-sm sm:text-base tracking-wide uppercase">Registro gratuito</span>
                    </a>

                    <span class="text-white/40 text-lg">✦</span>

                    <a href="{{ route('register', ['role' => 'cook']) }}"
                        class="inline-flex items-center gap-3 group hover:opacity-95 transition-opacity">
                        <span
                            class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-white/20 backdrop-blur-md shadow-inner text-white text-sm group-hover:scale-110 transition-transform">🚀</span>
                        <span class="font-black text-sm sm:text-base tracking-wide uppercase">Vendé con nosotros sin
                            costo</span>
                    </a>

                    <span class="text-white/40 text-lg">✦</span>

                    <a href="{{ route('register', ['role' => 'cook']) }}"
                        class="inline-flex items-center gap-3 group hover:opacity-95 transition-opacity">
                        <span
                            class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-white/20 backdrop-blur-md shadow-inner text-rose-200 text-sm group-hover:scale-110 transition-transform">♡</span>
                        <span class="font-black text-sm sm:text-base tracking-wide uppercase">Sé parte del cambio</span>
                    </a>

                    <span class="text-white/40 text-lg">✦</span>

                    <a href="{{ route('register', ['role' => 'cook']) }}"
                        class="inline-flex items-center gap-3 group hover:opacity-95 transition-opacity">
                        <span
                            class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-white/20 backdrop-blur-md shadow-inner text-amber-200 text-sm group-hover:scale-110 transition-transform">🧑‍🍳</span>
                        <span class="font-black text-sm sm:text-base tracking-wide uppercase">Cociná para tu barrio o ciudad
                            y convertilo en tu oficio</span>
                    </a>

                    <span class="text-white/40 text-lg">✦</span>
                </div>

                <!-- Bloque 2 duplicado idéntico para loop infinito perfecto -->
                <div class="flex items-center gap-8 sm:gap-14 shrink-0" aria-hidden="true">
                    <a href="{{ route('register', ['role' => 'cook']) }}"
                        class="inline-flex items-center gap-3 group hover:opacity-95 transition-opacity">
                        <span
                            class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-white/20 backdrop-blur-md shadow-inner text-amber-200 text-sm group-hover:scale-110 transition-transform">✨</span>
                        <span class="font-black text-sm sm:text-base tracking-wide uppercase">Registro gratuito</span>
                    </a>

                    <span class="text-white/40 text-lg">✦</span>

                    <a href="{{ route('register', ['role' => 'cook']) }}"
                        class="inline-flex items-center gap-3 group hover:opacity-95 transition-opacity">
                        <span
                            class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-white/20 backdrop-blur-md shadow-inner text-white text-sm group-hover:scale-110 transition-transform">🚀</span>
                        <span class="font-black text-sm sm:text-base tracking-wide uppercase">Vendé con nosotros sin
                            costo</span>
                    </a>

                    <span class="text-white/40 text-lg">✦</span>

                    <a href="{{ route('register', ['role' => 'cook']) }}"
                        class="inline-flex items-center gap-3 group hover:opacity-95 transition-opacity">
                        <span
                            class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-white/20 backdrop-blur-md shadow-inner text-rose-200 text-sm group-hover:scale-110 transition-transform">♡</span>
                        <span class="font-black text-sm sm:text-base tracking-wide uppercase">Sé parte del cambio</span>
                    </a>

                    <span class="text-white/40 text-lg">✦</span>

                    <a href="{{ route('register', ['role' => 'cook']) }}"
                        class="inline-flex items-center gap-3 group hover:opacity-95 transition-opacity">
                        <span
                            class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-white/20 backdrop-blur-md shadow-inner text-amber-200 text-sm group-hover:scale-110 transition-transform">🧑‍🍳</span>
                        <span class="font-black text-sm sm:text-base tracking-wide uppercase">Cociná para tu barrio o ciudad
                            y convertilo en tu oficio</span>
                    </a>

                    <span class="text-white/40 text-lg">✦</span>
                </div>
            </div>
        </section>


        <!-- ============================================================ -->
        <!-- 2. SECCIÓN: CONOCÉ A QUIENES COCINAN PARA VOS (SLIDER/CARDS) -->
        <!-- ============================================================ -->
        <section class="py-14 sm:py-20 relative" id="cocineros" x-data="{
                                                        timer: null,
                                                        scrollSlider(direction) {
                                                            const container = this.$refs.cardsContainer;
                                                            if (!container) return;
                                                            const cardWidth = container.querySelector('.cook-card')?.offsetWidth || 300;
                                                            const maxScroll = container.scrollWidth - container.clientWidth;

                                                            if (maxScroll <= 10) return;

                                                            // Loop infinito suave: al llegar al final vuelve al inicio
                                                            if (direction > 0 && container.scrollLeft >= maxScroll - 20) {
                                                                container.scrollTo({ left: 0, behavior: 'smooth' });
                                                            } else if (direction < 0 && container.scrollLeft <= 20) {
                                                                container.scrollTo({ left: maxScroll, behavior: 'smooth' });
                                                            } else {
                                                                container.scrollBy({ left: direction * (cardWidth + 20), behavior: 'smooth' });
                                                            }
                                                        },
                                                        startAutoplay() {
                                                            this.stopAutoplay();
                                                            this.timer = setInterval(() => {
                                                                this.scrollSlider(1);
                                                            }, 4000);
                                                        },
                                                        stopAutoplay() {
                                                            if (this.timer) {
                                                                clearInterval(this.timer);
                                                                this.timer = null;
                                                            }
                                                        }
                                                    }" x-init="startAutoplay()" @mouseenter="stopAutoplay()"
            @mouseleave="startAutoplay()" @touchstart.passive="stopAutoplay()" @touchend.passive="startAutoplay()">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">

                <!-- Título y Subtítulo Centrados -->
                <div class="text-center max-w-2xl mx-auto mb-10 sm:mb-12">
                    <h2 class="text-3xl sm:text-4xl lg:text-[2.6rem] font-extrabold tracking-tight">
                        <span class="text-gradient">
                            Conocé a quienes cocinan para vos
                        </span>
                    </h2>
                    <p class="mt-3 text-sm sm:text-base text-gray-600 font-normal">
                        Personas reales, con sus propias historias, en sus propias cocinas.
                    </p>
                </div>

                <!-- Controles de Navegación del Slider (Desktop y Mobile) -->
                <div class="flex items-center justify-end space-x-2 mb-4 pr-1">
                    <button @click="scrollSlider(-1); stopAutoplay(); startAutoplay()" type="button"
                        class="w-10 h-10 rounded-full bg-white hover:bg-orange-50 border border-gray-200 text-gray-700 hover:text-orange-600 flex items-center justify-center shadow-sm hover:shadow transition-all active:scale-95"
                        aria-label="Anterior cocinero">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <button @click="scrollSlider(1); stopAutoplay(); startAutoplay()" type="button"
                        class="w-10 h-10 rounded-full bg-white hover:bg-orange-50 border border-gray-200 text-gray-700 hover:text-orange-600 flex items-center justify-center shadow-sm hover:shadow transition-all active:scale-95"
                        aria-label="Siguiente cocinero">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>

                <!-- SLIDER CONTENEDOR (Scroll Snap nativo fluido + drag/swipe con Autoplay) -->
                <div x-ref="cardsContainer"
                    class="cook-slider flex gap-5 overflow-x-auto snap-x snap-mandatory py-4 px-1 -mx-4 sm:mx-0 px-4 sm:px-0">

                    @foreach($featuredCooks as $cook)
                        <div class="cook-card flex-none w-[270px] sm:w-[290px] lg:w-[calc(25%-15px)] snap-start group">
                            <div
                                class="bg-white rounded-3xl p-4 shadow-sm hover:shadow-xl border border-orange-100/60 hover:border-orange-200 transition-all duration-300 flex flex-col h-full transform hover:-translate-y-1">

                                <!-- Foto del Cocinero + Badge "En tu zona" -->
                                <div class="relative w-full h-44 rounded-2xl overflow-hidden bg-gray-100 mb-3.5">
                                    <img src="{{ $cook['photo'] }}" alt="Cocinero {{ $cook['name'] }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out"
                                        loading="lazy">

                                    <!-- Badge "En tu zona" -->
                                    <span
                                        class="absolute top-2.5 right-2.5 bg-white/90 backdrop-blur-md text-amber-700 text-[11px] font-semibold px-2.5 py-1 rounded-full shadow-sm flex items-center space-x-1 border border-orange-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        <span>En tu zona</span>
                                    </span>
                                </div>

                                <!-- Información del Cocinero -->
                                <div class="space-y-1 mb-3">
                                    <div class="flex items-center justify-between">
                                        <h3
                                            class="font-bold text-gray-700 text-base group-hover:text-orange-600 transition-colors">
                                            {{ $cook['name'] }}
                                        </h3>
                                    </div>
                                    <p class="text-xs text-gray-500 font-medium">
                                        {{ $cook['specialty'] }}
                                    </p>

                                    <!-- Ubicación con Pin -->
                                    <div class="flex items-center text-xs text-purple-700/80 pt-0.5">
                                        <svg class="w-3.5 h-3.5 mr-1 flex-shrink-0 text-purple-600" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <span class="truncate">{{ $cook['location'] }}</span>
                                    </div>

                                    <!-- Calificación y Reviews -->
                                    <div class="flex items-center space-x-1.5 text-xs pt-1">
                                        <span class="text-amber-500 font-bold">★</span>
                                        <span class="font-bold text-gray-700">{{ $cook['rating'] }}</span>
                                        <span class="text-gray-400 font-normal">({{ $cook['reviews_count'] }})</span>
                                    </div>
                                </div>

                                <!-- 3 Fotos Miniatura de sus Platos -->
                                <div class="grid grid-cols-3 gap-2 pt-2 border-t border-gray-100 mt-auto">
                                    @foreach($cook['dishes'] as $dishImg)
                                        <div class="w-full h-16 rounded-xl overflow-hidden bg-gray-50 border border-gray-100/80">
                                            <img src="{{ $dishImg }}" alt="Plato de {{ $cook['name'] }}"
                                                class="w-full h-full object-cover hover:scale-110 transition-transform duration-300"
                                                loading="lazy">
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Link al perfil / catálogo -->
                                <a href="{{ $cook['url'] }}"
                                    class="mt-3 block text-center py-2 px-3 rounded-xl bg-orange-50/60 hover:bg-orange-100/80 text-orange-700 font-semibold text-xs transition-colors">
                                    Ver platos y menú
                                </a>

                            </div>
                        </div>
                    @endforeach

                </div>
            </div>
        </section>


        <!-- ============================================================ -->
        <!-- 3. SECCIÓN: CADA COCINERO TIENE SU PROPIA COCINA             -->
        <!-- ============================================================ -->
        <section class="py-12 sm:py-16" id="historias">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div
                    class="bg-[#fef9f4] rounded-3xl sm:rounded-[2.5rem] p-6 sm:p-10 lg:p-14 border border-orange-100/80 shadow-sm relative overflow-hidden">

                    <!-- Orbs decorativos traseros -->
                    <div
                        class="absolute -right-16 -top-16 w-80 h-80 bg-rose-200/30 rounded-full filter blur-3xl pointer-events-none">
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 relative z-10">

                        <!-- Lado Izquierdo: Video / Foto Grande de Cocina (60%) -->
                        <div class="lg:col-span-7">
                            <div class="relative group">
                                <div class="overflow-hidden rounded-2xl sm:rounded-3xl shadow-xl border-4 border-white">
                                    <!-- <img src="{{ asset('assets/home/kitchen_mariana.jpg') }}"
                                                                                                alt="Cocinera Mariana en su cocina preparando ingredientes frescos"
                                                                                                class="w-full h-[320px] sm:h-[420px] object-cover group-hover:scale-105 transition-transform duration-700 ease-out"
                                                                                                loading="lazy"> -->
                                    <div
                                        class="aspect-video bg-gradient-to-br from-purple-200 via-pink-200 to-orange-200 rounded-2xl flex items-center justify-center text-8xl">
                                        <video class="w-full h-full rounded-2xl"
                                            poster="{{ asset('assets/front/cubierta.webp') }}" width="100%" height="auto"
                                            controls>
                                            <source src="{{ asset('assets/video/presentation.mp4') }}" type="video/mp4">
                                            Tu navegador no soporta la etiqueta de video.
                                        </video>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Lado Derecho: Contenido, Card Flotante y Firma Manuscrita (40%) -->
                        <div class="lg:col-span-5 space-y-6">

                            <div class="space-y-3">
                                <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight">
                                    <span class="text-gradient">
                                        Cada cocinero tiene su propia cocina
                                    </span>
                                </h2>
                                <p class="text-sm sm:text-base text-gray-600 leading-relaxed font-normal">
                                    Detrás de cada plato hay una persona, su historia y su hogar. Cocinamos en nuestras
                                    casas, con ingredientes reales y todo el cariño del mundo.
                                </p>
                            </div>

                            <!-- Card Flotante de Mariana López -->
                            <div
                                class="bg-white rounded-2xl p-5 shadow-lg border border-orange-100/90 space-y-3 transform hover:-translate-y-0.5 transition-transform">
                                <div class="flex items-center space-x-3.5">
                                    <img src="{{ asset('assets/home/cook_mariana.jpg') }}" alt="Mariana López"
                                        class="w-12 h-12 rounded-full object-cover border-2 border-orange-200">
                                    <div>
                                        <h4 class="font-bold text-gray-700 text-sm">Mariana López</h4>
                                        <div class="flex items-center text-xs text-purple-700 font-medium">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            </svg>
                                            <span>Córdoba Capital · 4 km</span>
                                        </div>
                                    </div>
                                </div>

                                <p class="text-xs sm:text-sm text-gray-600 italic leading-relaxed">
                                    "Cocino porque me encanta y porque compartir la comida también es una forma de cuidar."
                                </p>

                                <!-- Miniaturas de platos de Mariana -->
                                <div class="grid grid-cols-3 gap-2 pt-2 border-t border-gray-100">
                                    <div class="h-16 rounded-xl overflow-hidden bg-gray-100">
                                        <img src="{{ asset('assets/home/dish_mini_gnocchi.jpg') }}" alt="Gnocchi caseros"
                                            class="w-full h-full object-cover">
                                    </div>
                                    <div class="h-16 rounded-xl overflow-hidden bg-gray-100">
                                        <img src="{{ asset('assets/home/dish_mini_roast.jpg') }}" alt="Pollo al horno"
                                            class="w-full h-full object-cover">
                                    </div>
                                    <div class="h-16 rounded-xl overflow-hidden bg-gray-100">
                                        <img src="{{ asset('assets/front/ensalada_casera.png') }}" alt="Ensalada fresca"
                                            class="w-full h-full object-cover">
                                    </div>
                                </div>
                            </div>

                            <!-- Nota Manuscrita -->
                            <div class="pt-2 flex justify-end">
                                <p class="font-handwriting text-2xl sm:text-3xl text-purple-900 -rotate-2 select-none">
                                    Comida real, hecha en casa <span class="text-rose-500">♡</span>
                                </p>
                            </div>

                        </div>

                    </div>
                </div>
            </div>
        </section>


        <!-- ============================================================ -->
        <!-- 4. SECCIÓN: ASÍ FUNCIONA COCINARTE                           -->
        <!-- ============================================================ -->
        <section class="py-14 sm:py-20" id="como-funciona">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">

                <div class="text-center mb-12 sm:mb-16">
                    <h2 class="text-3xl sm:text-4xl lg:text-[2.6rem] font-extrabold tracking-tight text-gray-700">
                        Así funciona <span class="text-gradient">Cocinarte</span>
                    </h2>
                </div>

                <!-- SVG Defs para Gradientes de Íconos y Flechas -->
                <svg class="absolute w-0 h-0 overflow-hidden pointer-events-none" aria-hidden="true">
                    <defs>
                        <linearGradient id="step-icon-grad" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" stop-color="#f97316" />
                            <stop offset="50%" stop-color="#e11d48" />
                            <stop offset="100%" stop-color="#c026d3" />
                        </linearGradient>
                        <linearGradient id="step-arrow-grad" x1="0%" y1="0%" x2="100%" y2="0%">
                            <stop offset="0%" stop-color="#e11d48" />
                            <stop offset="100%" stop-color="#9333ea" />
                        </linearGradient>
                    </defs>
                </svg>

                <!-- 3 Pasos con Layout Horizontal y Flechas Conectoras -->
                <div
                    class="max-w-5xl mx-auto flex flex-col md:flex-row items-start justify-between gap-8 md:gap-4 lg:gap-6 relative">

                    <!-- Paso 1 -->
                    <div class="flex-1 flex items-start gap-3.5 sm:gap-4 group">
                        <!-- Círculo Número 1 -->
                        <div
                            class="w-10 h-10 sm:w-11 sm:h-11 rounded-full bg-gradient-to-tr from-orange-500 via-rose-500 to-pink-500 text-white font-extrabold text-base sm:text-lg flex items-center justify-center shadow-lg shadow-rose-500/25 shrink-0 mt-0.5 group-hover:scale-105 transition-transform duration-200">
                            1
                        </div>

                        <!-- Contenido: Icono + Título + Descripción -->
                        <div class="space-y-2.5 sm:space-y-3">
                            <div
                                class="w-12 h-12 flex items-center justify-start group-hover:scale-105 transition-transform duration-200">
                                <svg viewBox="0 0 24 24" fill="none" stroke="url(#step-icon-grad)" stroke-width="1.8"
                                    stroke-linecap="round" stroke-linejoin="round"
                                    class="w-11 h-11 sm:w-12 sm:h-12 drop-shadow-sm">
                                    <path d="M6 18h12v3H6z" />
                                    <path d="M6 18v-2" />
                                    <path d="M18 18v-2" />
                                    <path
                                        d="M6 16c-2 0-3.5-1.8-3-3.8C3.5 10 5.2 9 7 9.5 7.8 6.8 10.2 5 13 5c2.5 0 4.7 1.4 5.6 3.6 1.8-.3 3.4.8 3.9 2.5.6 2.2-.8 4.9-3.5 4.9" />
                                    <path d="M9 16v-5.5" />
                                    <path d="M12 16V11" />
                                    <path d="M15 16v-4.5" />
                                </svg>
                            </div>

                            <h3 class="font-extrabold text-gray-700 text-lg leading-tight">
                                Encontrá un cocinero
                            </h3>
                            <p class="text-xs sm:text-sm text-gray-500 leading-relaxed font-normal max-w-[240px]">
                                Explorá perfiles, leé sus historias y elegí a quién te inspire.
                            </p>
                        </div>
                    </div>

                    <!-- Flecha 1 -> 2 (Desktop) -->
                    <div class="hidden md:flex items-center justify-center pt-3 shrink-0 px-1 lg:px-2">
                        <svg class="w-7 h-7 lg:w-8 lg:h-8" fill="none" viewBox="0 0 24 24">
                            <path d="M4 12h14m-5-5l5 5-5 5" stroke="url(#step-arrow-grad)" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>

                    <!-- Flecha 1 -> 2 (Mobile vertical) -->
                    <div class="flex md:hidden items-center justify-center w-full py-1">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24">
                            <path d="M12 4v14m-5-5l5 5 5-5" stroke="url(#step-arrow-grad)" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>

                    <!-- Paso 2 -->
                    <div class="flex-1 flex items-start gap-3.5 sm:gap-4 group">
                        <!-- Círculo Número 2 -->
                        <div
                            class="w-10 h-10 sm:w-11 sm:h-11 rounded-full bg-gradient-to-tr from-orange-500 via-rose-500 to-pink-500 text-white font-extrabold text-base sm:text-lg flex items-center justify-center shadow-lg shadow-rose-500/25 shrink-0 mt-0.5 group-hover:scale-105 transition-transform duration-200">
                            2
                        </div>

                        <!-- Contenido: Icono + Título + Descripción -->
                        <div class="space-y-2.5 sm:space-y-3">
                            <div
                                class="w-12 h-12 flex items-center justify-start group-hover:scale-105 transition-transform duration-200">
                                <svg viewBox="0 0 24 24" fill="none" stroke="url(#step-icon-grad)" stroke-width="1.8"
                                    stroke-linecap="round" stroke-linejoin="round"
                                    class="w-11 h-11 sm:w-12 sm:h-12 drop-shadow-sm">
                                    <path d="M2 10h20" />
                                    <path d="M3 10c0 5.5 4 9 9 9s9-3.5 9-9" />
                                    <path d="M9 19c0 1.5 1.5 2 3 2s3-.5 3-2" />
                                    <path d="M9 7c.5-1.5-.5-2.5 0-4" />
                                    <path d="M12 7c.5-1.5-.5-2.5 0-4" />
                                    <path d="M15 7c.5-1.5-.5-2.5 0-4" />
                                    <path d="M16 10l2.5-3" />
                                    <path d="M6 10c1 0 1.5-.5 2-.5s1 .5 2 .5 1.5-.5 2-.5 1 .5 2 .5" />
                                </svg>
                            </div>

                            <h3 class="font-extrabold text-gray-700 text-lg leading-tight">
                                Elegí su comida
                            </h3>
                            <p class="text-xs sm:text-sm text-gray-500 leading-relaxed font-normal max-w-[240px]">
                                Mirá su menú, sus fotos y hacé tu pedido en pocos clics.
                            </p>
                        </div>
                    </div>

                    <!-- Flecha 2 -> 3 (Desktop) -->
                    <div class="hidden md:flex items-center justify-center pt-3 shrink-0 px-1 lg:px-2">
                        <svg class="w-7 h-7 lg:w-8 lg:h-8" fill="none" viewBox="0 0 24 24">
                            <path d="M4 12h14m-5-5l5 5-5 5" stroke="url(#step-arrow-grad)" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>

                    <!-- Flecha 2 -> 3 (Mobile vertical) -->
                    <div class="flex md:hidden items-center justify-center w-full py-1">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24">
                            <path d="M12 4v14m-5-5l5 5 5-5" stroke="url(#step-arrow-grad)" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>

                    <!-- Paso 3 -->
                    <div class="flex-1 flex items-start gap-3.5 sm:gap-4 group">
                        <!-- Círculo Número 3 -->
                        <div
                            class="w-10 h-10 sm:w-11 sm:h-11 rounded-full bg-gradient-to-tr from-orange-500 via-rose-500 to-pink-500 text-white font-extrabold text-base sm:text-lg flex items-center justify-center shadow-lg shadow-rose-500/25 shrink-0 mt-0.5 group-hover:scale-105 transition-transform duration-200">
                            3
                        </div>

                        <!-- Contenido: Icono + Título + Descripción -->
                        <div class="space-y-2.5 sm:space-y-3">
                            <div
                                class="w-12 h-12 flex items-center justify-start group-hover:scale-105 transition-transform duration-200">
                                <svg viewBox="0 0 24 24" fill="none" stroke="url(#step-icon-grad)" stroke-width="1.8"
                                    stroke-linecap="round" stroke-linejoin="round"
                                    class="w-11 h-11 sm:w-12 sm:h-12 drop-shadow-sm">
                                    <path d="M17 7.5V4h-2.2v1.7" />
                                    <path d="M3 10.5L12 3l9 7.5V21a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V10.5z" />
                                    <path d="M9 22v-5a3 3 0 0 1 6 0v5" />
                                </svg>
                            </div>

                            <h3 class="font-extrabold text-gray-700 text-lg leading-tight">
                                Recibila o retirala
                            </h3>
                            <p class="text-xs sm:text-sm text-gray-500 leading-relaxed font-normal max-w-[240px]">
                                Coordiná la entrega o pasá a buscarla en la cocina del cocinero.
                            </p>
                        </div>
                    </div>

                </div>
            </div>
        </section>


        <!-- ============================================================ -->
        <!-- 5. BANNER CTA COCINEROS: "¿COCINÁS PARA LOS TUYOS?"          -->
        <!-- ============================================================ -->
        <section class="py-10 sm:py-16" id="para-cocineros">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="relative overflow-hidden rounded-3xl sm:rounded-[2.5rem] bg-rose-500 text-white shadow-2xl">

                    <div class="grid grid-cols-1 lg:grid-cols-12 items-stretch">

                        <!-- Lado Izquierdo: Textos y Botón (degradé propio que termina en rose-500 sólido) -->
                        <div
                            class="lg:col-span-7 p-8 sm:p-12 lg:p-16 space-y-6 relative z-10 flex flex-col justify-center bg-gradient-to-b lg:bg-gradient-to-r from-orange-500 to-rose-500">

                            <!-- Icono Chef Outline -->
                            <div
                                class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center text-white">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4a3.5 3.5 0 00-3.4 2.7A3.5 3.5 0 005 10a3.5 3.5 0 002.5 3.37L7 18h10l-.5-4.63A3.5 3.5 0 0019 10a3.5 3.5 0 00-3.6-3.3A3.5 3.5 0 0012 4zM9 18h6v2H9v-2z" />
                                </svg>
                            </div>

                            <div class="space-y-2">
                                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black leading-tight tracking-tight">
                                    ¿Cocinás para los tuyos?<br>
                                    Podés cocinar para tu barrio.
                                </h2>
                                <p class="text-sm sm:text-base text-white/90 leading-relaxed font-normal pt-2 max-w-lg">
                                    Elegí qué cocinar, cuándo cocinar y a qué precio.<br>
                                    Vos decidís, nosotros te ayudamos a llegar a más personas.
                                </p>
                            </div>

                            <div class="pt-4 flex flex-wrap items-center gap-6">
                                <a href="{{ route('register', ['role' => 'cook']) }}"
                                    class="bg-white hover:bg-rose-50 text-rose-600 px-8 py-4 rounded-full font-bold text-sm sm:text-base shadow-xl hover:shadow-2xl hover:scale-105 active:scale-95 transition-all inline-flex items-center space-x-2">
                                    <span>Quiero ser cocinero</span>
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                    </svg>
                                </a>

                                <!-- Frase manuscrita blanca -->
                                <p class="font-handwriting text-2xl sm:text-3xl text-white/95 leading-tight select-none">
                                    Tu cocina también importa <span class="text-rose-200">♡</span>
                                </p>
                            </div>

                        </div>

                        <!-- Lado Derecho: Foto Cocinera con delantal floreado -->
                        <div class="lg:col-span-5 relative min-h-[300px] sm:min-h-[380px] lg:-ml-px">
                            <img src="{{ asset('assets/home/banner_cook.jpg') }}"
                                alt="Cocinera sonriente preparando un guiso casero"
                                class="absolute inset-0 w-full h-full object-cover object-center" loading="lazy">
                            <!-- Fusión: arranca en rose-500 sólido (mismo color final del box) y se vuelve transparente -->
                            <div
                                class="absolute inset-0 bg-gradient-to-b lg:bg-gradient-to-r from-rose-500 from-0% via-rose-500/50 via-25% to-transparent to-60% pointer-events-none">
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </section>


        <!-- ============================================================ -->
        <!-- 5.1 PROPUESTA DE VALOR: "VOS PONÉS EL SABOR..."             -->
        <!-- (Plataforma / Difusión / Crecimiento / Y mucho más)          -->
        <!-- ============================================================ -->
        <section class="vp-section" id="que-ofrecemos" aria-labelledby="vp-title">

            <!-- Fondo: orbes de color + grilla sutil -->
            <div class="vp-bg" aria-hidden="true">
                <span class="vp-orb vp-orb--1"></span>
                <span class="vp-orb vp-orb--2"></span>
                <span class="vp-orb vp-orb--3"></span>
                <span class="vp-grid-pattern"></span>
            </div>

            <div class="container mx-auto px-4 sm:px-6 lg:px-8">

                <!-- Encabezado -->
                <header class="vp-head vp-reveal">
                    <span class="vp-eyebrow"><span class="vp-eyebrow-dot"></span>Lo que ponemos nosotros</span>
                    <h2 class="vp-title" id="vp-title">
                        Vos ponés el sabor.<br>
                        <span class="vp-title-grad">Nosotros, todo lo demás.</span>
                    </h2>
                    <p class="vp-lead">
                        Sumarte a Cocinarte es tener un equipo detrás de tu cocina: una plataforma confiable,
                        difusión permanente y herramientas para que tu proyecto crezca.
                    </p>
                </header>

                <div class="vp-bento">

                    <!-- CARD 1: Plataforma abierta y segura -->
                    <article class="vp-card vp-card--platform vp-reveal" style="--d: 0ms">
                        <div class="vp-platform">
                            <div>
                                <div class="vp-card-head">
                                    <span class="vp-icon" aria-hidden="true">
                                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M12 3l7 3v5c0 4.5-3 8.3-7 10-4-1.7-7-5.5-7-10V6l7-3z" />
                                            <path d="M9 12l2 2 4-4" />
                                        </svg>
                                    </span>
                                    <span class="vp-kicker">01 · Tu espacio</span>
                                </div>
                                <h3>Un espacio propio, abierto y seguro para vender lo que cocinás</h3>
                                <p class="vp-text">
                                    Publicá tu menú, recibí pedidos y cobrá con pagos protegidos. Vos decidís qué,
                                    cuándo y a qué precio; nosotros cuidamos que cada operación sea clara y confiable.
                                </p>
                                <ul class="vp-checks">
                                    <li>
                                        <svg fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"
                                            aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Pagos protegidos
                                    </li>
                                    <li>
                                        <svg fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"
                                            aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Tus precios, tus horarios
                                    </li>
                                    <li>
                                        <svg fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"
                                            aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Pedidos ordenados
                                    </li>
                                </ul>
                            </div>

                            <!-- Mock de notificaciones (decorativo) -->
                            <div class="vp-mock" aria-hidden="true">
                                <div class="vp-notif vp-notif--main">
                                    <img src="{{ asset('assets/home/dish_mini_lasagna.jpg') }}" alt="" loading="lazy">
                                    <div>
                                        <div class="vp-notif-label">Nuevo pedido</div>
                                        <div class="vp-notif-body">2× Lasagna casera</div>
                                    </div>
                                    <div class="vp-notif-end">
                                        <div class="vp-notif-price">$ 14.800</div>
                                        <span class="vp-pill-ok">✓ Pago aprobado</span>
                                    </div>
                                </div>

                                <div class="vp-notif vp-notif--glass vp-notif--right">
                                    <span class="vp-notif-ico" style="background: rgba(74, 222, 128, .18); color: #4ade80;">
                                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="4" y="11" width="16" height="10" rx="2" />
                                            <path d="M8 11V7a4 4 0 0 1 8 0v4" />
                                        </svg>
                                    </span>
                                    <div>
                                        <div class="vp-notif-label">Cobro protegido</div>
                                        <div class="vp-notif-body">Dinero acreditado</div>
                                    </div>
                                </div>

                                <div class="vp-notif vp-notif--glass vp-notif--left">
                                    <span class="vp-notif-ico" style="background: rgba(251, 146, 60, .18); color: #fdba74;">
                                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="12" cy="12" r="9" />
                                            <path d="M12 7v5l3 2" />
                                        </svg>
                                    </span>
                                    <div>
                                        <div class="vp-notif-label">Retiro confirmado</div>
                                        <div class="vp-notif-body">Hoy · 20:30 hs</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </article>

                    <!-- CARD 2: Marketing y captación constante -->
                    <article class="vp-card vp-card--reach vp-reveal" style="--d: 120ms">
                        <div class="vp-card-head">
                            <span class="vp-icon" aria-hidden="true">
                                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 11v2a1 1 0 0 0 1 1h2l5 4V6L6 10H4a1 1 0 0 0-1 1z" />
                                    <path d="M15.5 8.5a5 5 0 0 1 0 7" />
                                    <path d="M18.5 5.5a9 9 0 0 1 0 13" />
                                </svg>
                            </span>
                            <span class="vp-kicker">02 · Difusión</span>
                        </div>
                        <h3>Comensales nuevos llegando a tu cocina</h3>
                        <p class="vp-text">
                            Hacemos la difusión por vos: redes, buscadores y acciones en tu zona para que cada semana
                            más vecinos descubran lo que cocinás.
                        </p>

                        <!-- Radar de comensales (decorativo) -->
                        <div class="vp-radar" aria-hidden="true">
                            <span class="vp-radar-ring"></span>
                            <span class="vp-radar-ring"></span>
                            <span class="vp-radar-ring"></span>
                            <span class="vp-radar-core">
                                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 21s-7-6.1-7-11.5a7 7 0 0 1 14 0C19 14.9 12 21 12 21z" />
                                    <circle cx="12" cy="9.5" r="2.5" />
                                </svg>
                            </span>
                            <span class="vp-avatar vp-avatar--1">MG</span>
                            <span class="vp-avatar vp-avatar--2">JP</span>
                            <span class="vp-avatar vp-avatar--3">LA</span>
                            <span class="vp-avatar vp-avatar--4">SR</span>
                            <span class="vp-avatar vp-avatar--5">+</span>
                        </div>
                    </article>

                    <!-- CARD 3: Opciones de crecimiento -->
                    <article class="vp-card vp-card--growth vp-reveal" style="--d: 0ms">
                        <div class="vp-card-head">
                            <span class="vp-icon" aria-hidden="true">
                                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 17l6-6 4 4 8-8" />
                                    <path d="M15 7h6v6" />
                                </svg>
                            </span>
                            <span class="vp-kicker">03 · Proyección</span>
                        </div>
                        <h3>Crecé a tu ritmo, hasta tener tu propia marca</h3>
                        <p class="vp-text">
                            Cada pedido suma reseñas y reputación. Con más visibilidad y nuevas herramientas, pasás de
                            cocinar para algunos vecinos a un emprendimiento gastronómico con nombre propio.
                        </p>

                        <!-- Gráfico de crecimiento (decorativo) -->
                        <div class="vp-chart" aria-hidden="true">
                            <svg viewBox="0 0 320 120">
                                <defs>
                                    <linearGradient id="vp-line-grad" x1="0" y1="0" x2="1" y2="0">
                                        <stop offset="0%" stop-color="#fb923c" />
                                        <stop offset="55%" stop-color="#f43f5e" />
                                        <stop offset="100%" stop-color="#c084fc" />
                                    </linearGradient>
                                    <linearGradient id="vp-area-grad" x1="0" y1="0" x2="0" y2="1">
                                        <stop offset="0%" stop-color="#f43f5e" stop-opacity=".35" />
                                        <stop offset="100%" stop-color="#f43f5e" stop-opacity="0" />
                                    </linearGradient>
                                </defs>
                                <path class="vp-chart-area" fill="url(#vp-area-grad)"
                                    d="M0,108 C40,104 60,96 90,91 C125,85 140,72 175,64 C210,56 230,42 260,31 C285,22 300,15 314,10 L314,120 L0,120 Z" />
                                <path class="vp-chart-line" pathLength="1" fill="none" stroke="url(#vp-line-grad)"
                                    stroke-width="3.5" stroke-linecap="round"
                                    d="M0,108 C40,104 60,96 90,91 C125,85 140,72 175,64 C210,56 230,42 260,31 C285,22 300,15 314,10" />
                                <circle class="vp-chart-dot-ring" cx="314" cy="10" r="5" fill="#c084fc" />
                                <circle cx="314" cy="10" r="5" fill="#fff" />
                            </svg>
                            <div class="vp-steps">
                                <span>Primeros pedidos</span>
                                <span>Clientes fieles</span>
                                <span>Tu propia marca</span>
                            </div>
                        </div>
                    </article>

                    <!-- CARD 4: Y mucho más -->
                    <article class="vp-card vp-card--more vp-reveal" style="--d: 120ms">
                        <div class="vp-card-head">
                            <span class="vp-icon" aria-hidden="true">
                                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 3l1.8 4.7L18.5 9.5l-4.7 1.8L12 16l-1.8-4.7L5.5 9.5l4.7-1.8L12 3z" />
                                    <path d="M19 15l.8 2.2L22 18l-2.2.8L19 21l-.8-2.2L16 18l2.2-.8L19 15z" />
                                </svg>
                            </span>
                            <span class="vp-kicker">04 · Siempre sumando</span>
                        </div>
                        <h3><span class="vp-title-grad">Y mucho más</span></h3>
                        <p class="vp-text">
                            Cocinarte evoluciona todo el tiempo con nuevas herramientas pensadas para tu cocina.
                        </p>

                        @php
                            $vpChipsTop = ['Perfil de cocinero propio', 'Panel de pedidos', 'Reseñas y calificaciones', 'Envío o retiro', 'Menú siempre actualizado', 'Soporte cercano'];
                            $vpChipsBottom = ['Comunidad de cocineros', 'Avisos al instante', 'Galería de tus platos', 'Cobros online', 'Perfil destacado', 'Estadísticas de ventas'];
                        @endphp

                        <!-- Lista accesible para lectores de pantalla -->
                        <ul class="sr-only">
                            @foreach (array_merge($vpChipsTop, $vpChipsBottom) as $chip)
                                <li>{{ $chip }}</li>
                            @endforeach
                        </ul>

                        <div class="vp-marquee" aria-hidden="true">
                            <div class="vp-marquee-track">
                                @foreach (array_merge($vpChipsTop, $vpChipsTop) as $chip)
                                    <span class="vp-chip">{{ $chip }}</span>
                                @endforeach
                            </div>
                            <div class="vp-marquee-track vp-marquee-track--rev">
                                @foreach (array_merge($vpChipsBottom, $vpChipsBottom) as $chip)
                                    <span class="vp-chip">{{ $chip }}</span>
                                @endforeach
                            </div>
                        </div>
                    </article>

                </div>

                <!-- CTA final -->
                <div class="vp-cta vp-reveal">
                    <a href="{{ route('register', ['role' => 'cook']) }}" class="vp-btn" id="vp-cta-register">
                        <span>Sumá tu cocina a Cocinarte</span>
                        <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                    <p class="vp-cta-note">Empezá hoy y crecé a tu ritmo.</p>
                </div>

            </div>
        </section>


        <!-- ============================================================ -->
        <!-- 6. SECCIÓN INFERIOR: 3 COLUMNAS                              -->
        <!-- (Comensal / Preguntas Frecuentes / Testimonios)             -->
        <!-- ============================================================ -->
        <section class="py-12 sm:py-20" id="faq">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8 items-stretch">

                    <!-- COLUMNA 1: ¿SOS COMENSAL? -->
                    <div
                        class="bg-[#fef9f5] rounded-3xl p-6 sm:p-7 border border-orange-100 flex flex-col justify-between shadow-sm">
                        <div>
                            <!-- Foto Plato en Manos -->
                            <div class="w-full h-44 rounded-2xl overflow-hidden mb-5 bg-gray-100 shadow-sm">
                                <img src="{{ asset('assets/home/dish_hands.jpg') }}"
                                    alt="Plato de pasta casera con albahaca"
                                    class="w-full h-full object-cover hover:scale-105 transition-transform duration-500">
                            </div>

                            <h3 class="text-xl font-bold text-rose-500">
                                ¿Sos comensal?
                            </h3>
                            <h4 class="text-base font-extrabold text-gray-700 mt-0.5 mb-2.5">
                                Disfrutá comida casera, cerca tuyo.
                            </h4>
                            <p class="text-xs sm:text-sm text-gray-600 leading-relaxed font-normal">
                                Comida rica, hecha con ingredientes reales, por personas de tu barrio. Apoyás a cocineros
                                independientes y vivís una experiencia única.
                            </p>
                        </div>

                        <div class="pt-6">
                            <a href="{{ route('marketplace.catalog') }}"
                                class="w-full btn-gradient text-white py-3 px-5 rounded-full font-bold text-xs sm:text-sm text-center shadow-md glow-effect hover:-translate-y-0.5 transition-all inline-flex items-center justify-center space-x-2">
                                <span>Explorar cocineros</span>
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </a>
                        </div>
                    </div>

                    <!-- COLUMNA 2: PREGUNTAS FRECUENTES (Acordeón) -->
                    <div class="bg-white rounded-3xl p-6 sm:p-7 border border-gray-100 shadow-sm flex flex-col justify-between"
                        x-data="{ openFaq: null }">
                        <div>
                            <h3 class="text-lg sm:text-xl font-bold text-gray-700 mb-5">
                                Preguntas frecuentes
                            </h3>

                            <div class="space-y-3">
                                <!-- FAQ 1 -->
                                <div class="border-b border-gray-100 pb-3">
                                    <button @click="openFaq = (openFaq === 1 ? null : 1)" type="button"
                                        class="w-full text-left flex items-center justify-between text-xs sm:text-sm font-semibold text-gray-700 hover:text-orange-600 transition-colors">
                                        <span class="pr-2">¿Cómo funciona Cocinarte?</span>
                                        <span class="text-gray-400 font-bold text-base transition-transform duration-200"
                                            :class="{ 'rotate-45 text-rose-500': openFaq === 1 }">+</span>
                                    </button>
                                    <div x-show="openFaq === 1" x-collapse
                                        class="mt-2 text-xs text-gray-500 leading-relaxed">
                                        Elegís un cocinero cerca tuyo, seleccionás sus platos caseros y decidís si querés
                                        envío a domicilio o retiro por su cocina. Pagás seguro por la web.
                                    </div>
                                </div>

                                <!-- FAQ 2 -->
                                <div class="border-b border-gray-100 pb-3">
                                    <button @click="openFaq = (openFaq === 2 ? null : 2)" type="button"
                                        class="w-full text-left flex items-center justify-between text-xs sm:text-sm font-semibold text-gray-700 hover:text-orange-600 transition-colors">
                                        <span class="pr-2">¿Cómo se paga?</span>
                                        <span class="text-gray-400 font-bold text-base transition-transform duration-200"
                                            :class="{ 'rotate-45 text-rose-500': openFaq === 2 }">+</span>
                                    </button>
                                    <div x-show="openFaq === 2" x-collapse
                                        class="mt-2 text-xs text-gray-500 leading-relaxed">
                                        Aceptamos MercadoPago con tarjetas de débito, crédito o dinero en cuenta,
                                        garantizando máxima seguridad en cada transacción.
                                    </div>
                                </div>

                                <!-- FAQ 3 -->
                                <div class="border-b border-gray-100 pb-3">
                                    <button @click="openFaq = (openFaq === 3 ? null : 3)" type="button"
                                        class="w-full text-left flex items-center justify-between text-xs sm:text-sm font-semibold text-gray-700 hover:text-orange-600 transition-colors">
                                        <span class="pr-2">¿Cuánto tarda la entrega o retiro?</span>
                                        <span class="text-gray-400 font-bold text-base transition-transform duration-200"
                                            :class="{ 'rotate-45 text-rose-500': openFaq === 3 }">+</span>
                                    </button>
                                    <div x-show="openFaq === 3" x-collapse
                                        class="mt-2 text-xs text-gray-500 leading-relaxed">
                                        Cada cocinero especifica el tiempo de elaboración (habitualmente entre 30 y 60
                                        minutos, o pedidos programados para el mediodía/noche).
                                    </div>
                                </div>

                                <!-- FAQ 4 -->
                                <div class="border-b border-gray-100 pb-3">
                                    <button @click="openFaq = (openFaq === 4 ? null : 4)" type="button"
                                        class="w-full text-left flex items-center justify-between text-xs sm:text-sm font-semibold text-gray-700 hover:text-orange-600 transition-colors">
                                        <span class="pr-2">¿Puedo pedir para varias personas?</span>
                                        <span class="text-gray-400 font-bold text-base transition-transform duration-200"
                                            :class="{ 'rotate-45 text-rose-500': openFaq === 4 }">+</span>
                                    </button>
                                    <div x-show="openFaq === 4" x-collapse
                                        class="mt-2 text-xs text-gray-500 leading-relaxed">
                                        ¡Claro que sí! Podés seleccionar múltiples porciones de un mismo cocinero o armar un
                                        menú familiar.
                                    </div>
                                </div>

                                <!-- FAQ 5 -->
                                <div class="pb-1">
                                    <button @click="openFaq = (openFaq === 5 ? null : 5)" type="button"
                                        class="w-full text-left flex items-center justify-between text-xs sm:text-sm font-semibold text-gray-700 hover:text-orange-600 transition-colors">
                                        <span class="pr-2">¿Qué pasa si tengo un problema con mi pedido?</span>
                                        <span class="text-gray-400 font-bold text-base transition-transform duration-200"
                                            :class="{ 'rotate-45 text-rose-500': openFaq === 5 }">+</span>
                                    </button>
                                    <div x-show="openFaq === 5" x-collapse
                                        class="mt-2 text-xs text-gray-500 leading-relaxed">
                                        Contás con soporte directo por WhatsApp y nuestro equipo de atención para resolver
                                        cualquier inconveniente de inmediato.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- COLUMNA 3: LO QUE DICEN NUESTROS COMENSALES (Testimonios Slider con Autoplay y 4 items) -->
                    <div class="bg-white rounded-3xl p-6 sm:p-7 border border-gray-100 shadow-sm flex flex-col justify-between"
                        x-data="{
                                                                        current: 0,
                                                                        timer: null,
                                                                        items: [
                                                                            {
                                                                                name: 'Laura G.',
                                                                                location: 'Córdoba Capital',
                                                                                text: 'La comida de Doña Rosa es espectacular. Todo casero, riquísimo y con mucho amor. Ya pedí varias veces y siempre es un placer.',
                                                                                stars: 5,
                                                                                avatar: '{{ asset('assets/home/cook_mariana.jpg') }}'
                                                                            },
                                                                            {
                                                                                name: 'Martín P.',
                                                                                location: 'Villa Allende',
                                                                                text: 'Increíble la calidad de las pastas de Leo. Se nota la masa fresca de verdad. Llegó caliente y en horario perfecto.',
                                                                                stars: 5,
                                                                                avatar: '{{ asset('assets/home/cook_leo.jpg') }}'
                                                                            },
                                                                            {
                                                                                name: 'Sofía M.',
                                                                                location: 'Córdoba Capital',
                                                                                text: 'Cocinamos poco por el trabajo y Cocinarte nos salvó las cenas. Comida sana, como la que hacía mi abuela.',
                                                                                stars: 5,
                                                                                avatar: '{{ asset('assets/home/cook_dona_rosa.jpg') }}'
                                                                            },
                                                                            {
                                                                                name: 'Gonzalo R.',
                                                                                location: 'Bell Ville',
                                                                                text: 'Pedir platos caseros recién hechos me cambió la semana. Los sabores son auténticos y la calidad es excelente.',
                                                                                stars: 5,
                                                                                avatar: '{{ asset('assets/home/cook_fernando.jpg') }}'
                                                                            }
                                                                        ],
                                                                        startAutoplay() {
                                                                            this.stopAutoplay();
                                                                            this.timer = setInterval(() => {
                                                                                this.current = (this.current + 1) % this.items.length;
                                                                            }, 4500);
                                                                        },
                                                                        stopAutoplay() {
                                                                            if (this.timer) {
                                                                                clearInterval(this.timer);
                                                                                this.timer = null;
                                                                            }
                                                                        }
                                                                    }" x-init="startAutoplay()"
                        @mouseenter="stopAutoplay()" @mouseleave="startAutoplay()" @touchstart.passive="stopAutoplay()"
                        @touchend.passive="startAutoplay()">

                        <div>
                            <h3 class="text-lg sm:text-xl font-bold text-gray-700 mb-6">
                                Lo que dicen nuestros comensales
                            </h3>

                            <!-- Testimonio Activo con transición suave -->
                            <div class="space-y-4 min-h-[140px]">
                                <div class="flex items-center space-x-3.5">
                                    <img :src="items[current].avatar" :alt="items[current].name"
                                        class="w-11 h-11 rounded-full object-cover border-2 border-orange-200">
                                    <div>
                                        <h4 class="font-bold text-gray-700 text-sm" x-text="items[current].name"></h4>
                                        <p class="text-xs text-gray-400" x-text="items[current].location"></p>
                                    </div>
                                </div>

                                <p class="text-xs sm:text-sm text-gray-600 italic leading-relaxed min-h-[50px]"
                                    x-text="'&ldquo;' + items[current].text + '&rdquo;'">
                                </p>

                                <!-- Estrellas Doradas -->
                                <div class="flex items-center space-x-1 text-amber-400 text-sm pt-2">
                                    <span>★</span><span>★</span><span>★</span><span>★</span><span>★</span>
                                </div>
                            </div>
                        </div>

                        <!-- Dots de paginación del testimonio (4 items) -->
                        <div class="flex items-center justify-center space-x-2 pt-6">
                            <template x-for="(item, index) in items" :key="index">
                                <button @click="current = index; stopAutoplay(); startAutoplay()" type="button"
                                    class="h-2.5 rounded-full transition-all duration-300"
                                    :class="current === index ? 'bg-orange-500 w-6' : 'bg-gray-200 hover:bg-gray-300 w-2.5'"
                                    :aria-label="'Ver testimonio ' + (index + 1)">
                                </button>
                            </template>
                        </div>

                    </div>

                </div>
            </div>
        </section>

    </div>

@endsection

@push('meta')
    <script type="application/ld+json">
                                                                                        {
                                                                                          "@@context": "https://schema.org",
                                                                                          "@@graph": [
                                                                                            {
                                                                                              "@@type": "Organization",
                                                                                              "@@id": "{{ route('home') }}#organization",
                                                                                              "name": "{{ $globalSettings['site_name'] ?? 'Cocinarte' }}",
                                                                                              "url": "{{ route('home') }}",
                                                                                              "logo": {
                                                                                                "@@type": "ImageObject",
                                                                                                "url": "{{ asset('assets/front/logo-8.webp') }}",
                                                                                                "caption": "{{ $globalSettings['site_name'] ?? 'Cocinarte' }}"
                                                                                              }
                                                                                            },
                                                                                            {
                                                                                              "@@type": "WebSite",
                                                                                              "@@id": "{{ route('home') }}#website",
                                                                                              "url": "{{ route('home') }}",
                                                                                              "name": "{{ $globalSettings['site_name'] ?? 'Cocinarte' }}",
                                                                                              "description": "{{ $globalSettings['meta_description'] ?? 'La comida casera de personas como vos' }}",
                                                                                              "publisher": {
                                                                                                "@@id": "{{ route('home') }}#organization"
                                                                                              }
                                                                                            }
                                                                                          ]
                                                                                        }
                                                                                        </script>
@endpush

@push('scripts')
    <script>
        // 5.1 Propuesta de valor: reveal on scroll + spotlight que sigue al cursor
        (() => {
            const section = document.getElementById('que-ofrecemos');
            if (!section) return;

            if ('IntersectionObserver' in window) {
                section.classList.add('vp-ready');
                const io = new IntersectionObserver((entries) => {
                    entries.forEach((entry) => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('is-visible');
                            io.unobserve(entry.target);
                        }
                    });
                }, { threshold: 0.15, rootMargin: '0px 0px -40px 0px' });
                section.querySelectorAll('.vp-reveal').forEach((el) => io.observe(el));
            }

            section.querySelectorAll('.vp-card').forEach((card) => {
                card.addEventListener('pointermove', (ev) => {
                    const rect = card.getBoundingClientRect();
                    card.style.setProperty('--mx', (ev.clientX - rect.left) + 'px');
                    card.style.setProperty('--my', (ev.clientY - rect.top) + 'px');
                });
            });
        })();
    </script>
@endpush