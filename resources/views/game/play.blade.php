<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>لعبة XO الذكية - {{ $game->id }}</title>
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;900&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Tajawal', 'Cairo', 'Arial', sans-serif;
            background: #0a0a0f;
            background-image: 
                radial-gradient(at 20% 30%, rgba(139, 92, 246, 0.15) 0px, transparent 50%),
                radial-gradient(at 80% 70%, rgba(236, 72, 153, 0.15) 0px, transparent 50%),
                radial-gradient(at 50% 50%, rgba(59, 130, 246, 0.1) 0px, transparent 50%);
            background-attachment: fixed;
            min-height: 100vh;
            color: #fff;
            overflow-x: hidden;
            -webkit-tap-highlight-color: transparent;
            padding: 0;
            margin: 0;
        }

        .game-wrapper {
            position: relative;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px 10px 20px;
            width: 100%;
        }
        @media (min-width: 640px) {
            .game-wrapper {
                padding: 20px;
            }
        }

        .game-container {
            background: #1a1a24;
            border: 2px solid #2d2d3a;
            border-radius: 0;
            padding: 0;
            max-width: 1200px;
            width: 100%;
            position: relative;
            overflow: hidden;
            box-shadow: 
                0 0 0 1px rgba(139, 92, 246, 0.1),
                0 20px 60px rgba(0, 0, 0, 0.5),
                inset 0 1px 0 rgba(255, 255, 255, 0.05);
        }
        @media (min-width: 768px) {
            .game-container {
                border-radius: 12px;
                padding: 0;
            }
        }
        @media (min-width: 640px) {
            .game-container {
                border-radius: 28px;
                padding: 24px;
            }
        }
        @media (min-width: 1024px) {
            .game-container {
                border-radius: 32px;
                padding: 32px;
            }
        }

        .game-container::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.05), transparent);
            animation: shimmer 3s infinite;
        }

        @keyframes shimmer {
            0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
            100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
        }

        .game-header {
            background: linear-gradient(180deg, #1e1e2e 0%, #1a1a24 100%);
            border-bottom: 1px solid #2d2d3a;
            padding: 20px 16px;
            text-align: center;
            position: relative;
            z-index: 1;
        }
        @media (min-width: 640px) {
            .game-header {
                padding: 24px 20px;
            }
        }

        .game-title {
            font-size: 1.5rem;
            color: #fff;
            margin: 0;
            font-weight: 800;
            letter-spacing: -0.5px;
            text-transform: uppercase;
        }
        @media (min-width: 480px) {
            .game-title {
                font-size: 2.25rem;
            }
        }
        @media (min-width: 640px) {
            .game-title {
                font-size: 2.75rem;
            }
        }
        @media (min-width: 1024px) {
            .game-title {
                font-size: 3rem;
            }
        }

        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .game-mode {
            font-size: 0.75rem;
            color: #8b8b9e;
            margin-top: 8px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        @media (min-width: 640px) {
            .game-mode {
                font-size: 1rem;
            }
        }
        @media (min-width: 1024px) {
            .game-mode {
                font-size: 1.125rem;
            }
        }

        .players-arena {
            display: grid;
            grid-template-columns: 1fr;
            gap: 0;
            position: relative;
            z-index: 1;
            background: #151520;
        }
        @media (min-width: 768px) {
            .players-arena {
                grid-template-columns: 280px 1fr 280px;
                gap: 0;
            }
        }

        .player-card {
            background: #1a1a24;
            border: none;
            border-right: 1px solid #2d2d3a;
            padding: 24px 16px;
            text-align: center;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        @media (min-width: 768px) {
            .player-card {
                padding: 32px 20px;
            }
        }
        .player-card:last-child {
            border-right: none;
            border-left: 1px solid #2d2d3a;
        }

        .player-card.active {
            background: linear-gradient(180deg, #2a1a3d 0%, #1a1a24 100%);
            border-right-color: #8b5cf6;
            box-shadow: inset 0 0 30px rgba(139, 92, 246, 0.2);
        }
        .player-card.active::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #8b5cf6, #ec4899);
        }

        .player-avatar {
            width: 64px;
            height: 64px;
            border-radius: 12px;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            margin: 0 auto 16px;
            position: relative;
            overflow: hidden;
            border: 2px solid #2d2d3a;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }
        @media (min-width: 768px) {
            .player-avatar {
                width: 80px;
                height: 80px;
                font-size: 2rem;
            }
        }

        .player-card.active .player-avatar {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        .player-name {
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 12px;
            color: #e5e5e5;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        @media (min-width: 768px) {
            .player-name {
                font-size: 1rem;
            }
        }

        .player-symbol {
            font-size: 2.5rem;
            font-weight: 900;
            margin-bottom: 16px;
            color: #fff;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        }
        @media (min-width: 768px) {
            .player-symbol {
                font-size: 3rem;
            }
        }

        .player-stats {
            display: flex;
            justify-content: space-around;
            margin-top: 16px;
            padding-top: 16px;
            border-top: 1px solid #2d2d3a;
            gap: 12px;
        }

        .stat-item {
            text-align: center;
            flex: 1;
        }

        .stat-value {
            font-size: 1.125rem;
            font-weight: 700;
            color: #8b5cf6;
            line-height: 1.2;
        }
        @media (min-width: 768px) {
            .stat-value {
                font-size: 1.25rem;
            }
        }

        .stat-label {
            font-size: 0.7rem;
            color: #8b8b9e;
            margin-top: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .game-board-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 32px 16px;
            background: #151520;
            min-height: 400px;
        }
        @media (min-width: 768px) {
            .game-board-section {
                padding: 48px 32px;
                min-height: 500px;
            }
        }

        .board-wrapper {
            position: relative;
            width: 100%;
            max-width: 500px;
        }

        .game-board {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
            background: #0f0f15;
            padding: 16px;
            border-radius: 0;
            border: 2px solid #2d2d3a;
            width: 100%;
            max-width: 100%;
            box-shadow: 
                inset 0 2px 8px rgba(0, 0, 0, 0.5),
                0 0 0 1px rgba(139, 92, 246, 0.1);
        }
        @media (min-width: 480px) {
            .game-board {
                gap: 12px;
                padding: 20px;
            }
        }
        @media (min-width: 768px) {
            .game-board {
                gap: 16px;
                padding: 24px;
                border-radius: 8px;
            }
        }

        .board-cell {
            aspect-ratio: 1;
            width: 100%;
            background: #1a1a24;
            border: 2px solid #2d2d3a;
            border-radius: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            font-weight: 900;
            cursor: pointer;
            transition: all 0.15s ease;
            position: relative;
            overflow: hidden;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            touch-action: manipulation;
            min-height: 70px;
        }
        @media (min-width: 480px) {
            .board-cell {
                font-size: 2.5rem;
                min-height: 90px;
            }
        }
        @media (min-width: 768px) {
            .board-cell {
                font-size: 3.5rem;
                border-radius: 4px;
                min-height: 120px;
            }
        }

        .board-cell::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: radial-gradient(circle, rgba(0, 210, 255, 0.3) 0%, transparent 70%);
            transform: translate(-50%, -50%);
            transition: all 0.5s ease;
        }

        .board-cell:hover:not(.disabled),
        .board-cell:active:not(.disabled) {
            background: #222230;
            border-color: #8b5cf6;
            box-shadow: 
                0 0 0 2px rgba(139, 92, 246, 0.3),
                inset 0 0 20px rgba(139, 92, 246, 0.1);
        }
        @media (min-width: 768px) {
            .board-cell:hover:not(.disabled) {
                transform: scale(1.02);
            }
        }

        .board-cell:hover:not(.disabled)::before {
            width: 150%;
            height: 150%;
        }

        .board-cell.x {
            color: #f87171;
            background: linear-gradient(135deg, #2a1a1a 0%, #1a1a24 100%);
            border-color: #f87171;
            animation: xAppear 0.3s ease;
            box-shadow: 
                0 0 0 2px rgba(248, 113, 113, 0.2),
                inset 0 0 20px rgba(248, 113, 113, 0.1);
        }

        .board-cell.o {
            color: #60a5fa;
            background: linear-gradient(135deg, #1a1a2a 0%, #1a1a24 100%);
            border-color: #60a5fa;
            animation: oAppear 0.3s ease;
            box-shadow: 
                0 0 0 2px rgba(96, 165, 250, 0.2),
                inset 0 0 20px rgba(96, 165, 250, 0.1);
        }

        @keyframes xAppear {
            0% { 
                transform: scale(0) rotate(-180deg);
                opacity: 0;
            }
            60% {
                transform: scale(1.1) rotate(10deg);
            }
            100% { 
                transform: scale(1) rotate(0deg);
                opacity: 1;
            }
        }

        @keyframes oAppear {
            0% { 
                transform: scale(0);
                opacity: 0;
            }
            50% { 
                transform: scale(1.15);
            }
            100% { 
                transform: scale(1);
                opacity: 1;
            }
        }

        .board-cell.disabled {
            cursor: not-allowed;
            opacity: 0.7;
        }

        .game-status {
            text-align: center;
            padding: 16px 20px;
            margin: 16px 0;
            border-radius: 16px;
            font-size: 1.25rem;
            font-weight: 700;
            display: none;
            animation: statusAppear 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
        }
        @media (min-width: 640px) {
            .game-status {
                padding: 20px 24px;
                margin: 20px 0;
                font-size: 1.5rem;
                border-radius: 18px;
            }
        }
        @media (min-width: 1024px) {
            .game-status {
                padding: 24px 28px;
                margin: 24px 0;
                font-size: 1.75rem;
                border-radius: 20px;
            }
        }

        @keyframes statusAppear {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .status-win {
            background: linear-gradient(135deg, #22c55e, #16a34a);
            color: white;
            box-shadow: 0 0 20px rgba(34, 197, 94, 0.4);
        }

        .status-lose {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            box-shadow: 0 0 20px rgba(239, 68, 68, 0.4);
        }

        .status-draw {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
            box-shadow: 0 0 20px rgba(245, 158, 11, 0.4);
        }

        .speed-round-indicator {
            background: linear-gradient(135deg, #ec4899, #f43f5e);
            color: white;
            padding: 16px 20px;
            border-radius: 16px;
            text-align: center;
            margin: 16px 0;
            animation: speedRoundPulse 1.5s infinite;
            display: none;
            border: 2px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 0 20px rgba(236, 72, 153, 0.5);
            position: relative;
            z-index: 10;
            backdrop-filter: blur(10px);
        }
        @media (min-width: 640px) {
            .speed-round-indicator {
                padding: 20px 24px;
                margin: 20px 0;
                border-radius: 18px;
            }
        }
        @media (min-width: 1024px) {
            .speed-round-indicator {
                padding: 24px 28px;
                margin: 24px 0;
                border-radius: 20px;
            }
        }

        @keyframes speedRoundPulse {
            0%, 100% { 
                transform: scale(1); 
                box-shadow: 0 0 20px rgba(248, 87, 166, 0.5);
            }
            50% { 
                transform: scale(1.02); 
                box-shadow: 0 0 30px rgba(248, 87, 166, 0.8);
            }
        }

        .speed-round-title {
            font-size: 1.125rem;
            font-weight: 700;
            margin-bottom: 8px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
        @media (min-width: 640px) {
            .speed-round-title {
                font-size: 1.25rem;
                margin-bottom: 10px;
            }
        }
        @media (min-width: 1024px) {
            .speed-round-title {
                font-size: 1.5rem;
                margin-bottom: 12px;
            }
        }

        /* أنماط البطاقات */
        .power-ups-section {
            margin: 24px 0;
            padding: 24px 16px;
            background: transparent;
            position: relative;
            overflow: visible;
        }
        @media (min-width: 640px) {
            .power-ups-section {
                padding: 32px 24px;
            }
        }
        @media (min-width: 1024px) {
            .power-ups-section {
                padding: 40px 32px;
            }
        }
        
        .power-ups-title {
            text-align: center;
            font-size: 1.5rem;
            margin-bottom: 24px;
            color: #fff;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            position: relative;
            z-index: 1;
        }
        @media (min-width: 640px) {
            .power-ups-title {
                font-size: 1.75rem;
                margin-bottom: 28px;
            }
        }
        @media (min-width: 1024px) {
            .power-ups-title {
                font-size: 2rem;
                margin-bottom: 32px;
            }
        }
        
        .power-ups-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 16px;
            position: relative;
            z-index: 1;
        }
        @media (min-width: 480px) {
            .power-ups-grid {
                grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
                gap: 20px;
            }
        }
        @media (min-width: 768px) {
            .power-ups-grid {
                grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
                gap: 24px;
            }
        }
        
        .power-up-card {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            border: 2px solid rgba(139, 92, 246, 0.3);
            border-radius: 20px;
            padding: 20px 16px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            touch-action: manipulation;
            box-shadow: 
                0 4px 6px rgba(0, 0, 0, 0.1),
                0 10px 20px rgba(0, 0, 0, 0.15),
                0 0 0 1px rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            min-height: 180px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        @media (min-width: 640px) {
            .power-up-card {
                padding: 24px 20px;
                border-radius: 24px;
                min-height: 200px;
            }
        }
        @media (min-width: 1024px) {
            .power-up-card {
                padding: 28px 24px;
                min-height: 220px;
            }
        }
        
        .power-up-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(139, 92, 246, 0.1) 0%, rgba(236, 72, 153, 0.1) 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
            pointer-events: none;
        }
        
        .power-up-card:hover:not(.used):not(.unaffordable),
        .power-up-card:active:not(.used):not(.unaffordable) {
            transform: translateY(-8px);
            border-color: #8b5cf6;
            box-shadow: 
                0 8px 16px rgba(139, 92, 246, 0.3),
                0 20px 40px rgba(139, 92, 246, 0.2),
                0 0 0 2px rgba(139, 92, 246, 0.3);
        }
        .power-up-card:hover:not(.used):not(.unaffordable)::before {
            opacity: 1;
        }
        @media (min-width: 768px) {
            .power-up-card:hover:not(.used):not(.unaffordable) {
                transform: translateY(-12px) scale(1.02);
            }
        }
        
        .power-up-card.used {
            background: linear-gradient(135deg, #1a1a24 0%, #0f0f15 100%);
            cursor: not-allowed;
            opacity: 0.5;
            border-color: rgba(139, 92, 246, 0.1);
            box-shadow: none;
        }
        
        .power-up-card.used::after {
            content: '✓';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 3.5rem;
            color: rgba(139, 92, 246, 0.3);
            font-weight: bold;
            z-index: 2;
        }
        
        .power-up-card.unaffordable {
            background: linear-gradient(135deg, #1a1a24 0%, #0f0f15 100%);
            cursor: not-allowed;
            opacity: 0.6;
            border-color: rgba(239, 68, 68, 0.2);
            box-shadow: none;
        }
        
        .power-up-card.unaffordable::after {
            content: '🔒';
            position: absolute;
            top: 12px;
            right: 12px;
            font-size: 1.25rem;
            opacity: 0.6;
            z-index: 2;
        }
        @media (min-width: 640px) {
            .power-up-card.unaffordable::after {
                top: 16px;
                right: 16px;
                font-size: 1.5rem;
            }
        }
        
        .power-up-icon {
            font-size: 3rem;
            margin-bottom: 12px;
            display: block;
            filter: drop-shadow(0 2px 8px rgba(0, 0, 0, 0.3));
            animation: iconFloat 3s ease-in-out infinite;
            position: relative;
            z-index: 1;
            line-height: 1;
        }
        @media (min-width: 640px) {
            .power-up-icon {
                font-size: 3.5rem;
                margin-bottom: 16px;
            }
        }
        @media (min-width: 1024px) {
            .power-up-icon {
                font-size: 4rem;
            }
        }
        
        @keyframes iconFloat {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-8px); }
        }
        
        .power-up-name {
            font-weight: 700;
            margin-bottom: 12px;
            font-size: 1rem;
            color: #fff;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
            position: relative;
            z-index: 1;
            line-height: 1.3;
        }
        @media (min-width: 640px) {
            .power-up-name {
                font-size: 1.125rem;
                margin-bottom: 14px;
            }
        }
        @media (min-width: 1024px) {
            .power-up-name {
                font-size: 1.25rem;
            }
        }
        
        .power-up-cost {
            font-weight: 700;
            margin: 12px 0;
            padding: 8px 16px;
            border-radius: 12px;
            font-size: 0.875rem;
            display: inline-block;
            position: relative;
            z-index: 1;
            transition: all 0.3s ease;
            min-width: 80px;
        }
        @media (min-width: 640px) {
            .power-up-cost {
                font-size: 0.9375rem;
                padding: 10px 18px;
            }
        }

        .power-up-cost.affordable {
            background: linear-gradient(135deg, rgba(139, 92, 246, 0.3), rgba(139, 92, 246, 0.2));
            color: #a78bfa;
            border: 2px solid #8b5cf6;
            box-shadow: 0 2px 8px rgba(139, 92, 246, 0.3);
        }
        
        .power-up-card:hover:not(.used):not(.unaffordable) .power-up-cost.affordable {
            box-shadow: 0 4px 12px rgba(139, 92, 246, 0.5);
            transform: scale(1.05);
            background: linear-gradient(135deg, rgba(139, 92, 246, 0.4), rgba(139, 92, 246, 0.3));
        }

        .power-up-cost.unaffordable {
            background: rgba(239, 68, 68, 0.15);
            color: #f87171;
            border: 2px solid rgba(239, 68, 68, 0.3);
        }
        
        .power-up-desc {
            font-size: 0.75rem;
            opacity: 0.8;
            line-height: 1.5;
            color: rgba(255, 255, 255, 0.7);
            margin-top: 8px;
            position: relative;
            z-index: 1;
        }
        @media (min-width: 640px) {
            .power-up-desc {
                font-size: 0.8125rem;
            }
        }
        
        .power-up-badge {
            position: absolute;
            bottom: 12px;
            left: 50%;
            transform: translateX(-50%);
            background: linear-gradient(135deg, rgba(139, 92, 246, 0.3), rgba(236, 72, 153, 0.3));
            color: #fff;
            padding: 6px 12px;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 700;
            border: 1px solid rgba(139, 92, 246, 0.5);
            animation: badgePulse 2s ease-in-out infinite;
            z-index: 2;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        @media (min-width: 640px) {
            .power-up-badge {
                font-size: 0.75rem;
                padding: 8px 14px;
            }
        }
        
        @keyframes badgePulse {
            0%, 100% { 
                opacity: 0.9; 
                transform: translateX(-50%) scale(1);
                box-shadow: 0 0 10px rgba(139, 92, 246, 0.3);
            }
            50% { 
                opacity: 1; 
                transform: translateX(-50%) scale(1.05);
                box-shadow: 0 0 20px rgba(139, 92, 246, 0.5);
            }
        }
        
        .power-up-card.clickable {
            animation: clickableGlow 2s ease-in-out infinite;
        }
        
        @keyframes clickableGlow {
            0%, 100% { 
                border-color: rgba(139, 92, 246, 0.3);
                box-shadow: 
                    0 4px 6px rgba(0, 0, 0, 0.1),
                    0 10px 20px rgba(0, 0, 0, 0.15);
            }
            50% { 
                border-color: rgba(139, 92, 246, 0.6);
                box-shadow: 
                    0 6px 12px rgba(139, 92, 246, 0.2),
                    0 15px 30px rgba(139, 92, 246, 0.15);
            }
        }

        .computer-thinking {
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
            display: none;
        }

        .thinking-animation {
            display: inline-flex;
            align-items: center;
            gap: 15px;
        }

        .thinking-dots {
            display: flex;
            gap: 5px;
        }

        .thinking-dot {
            width: 10px;
            height: 10px;
            background: #00d2ff;
            border-radius: 50%;
            animation: thinking 1.4s infinite ease-in-out both;
        }

        .thinking-dot:nth-child(1) { animation-delay: -0.32s; }
        .thinking-dot:nth-child(2) { animation-delay: -0.16s; }

        @keyframes thinking {
            0%, 80%, 100% { transform: scale(0); }
            40% { transform: scale(1); }
        }

        .game-controls {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 30px;
            position: relative;
            z-index: 1;
        }

        .control-btn {
            padding: 12px 24px;
            border: none;
            border-radius: 16px;
            font-size: 0.875rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            position: relative;
            overflow: hidden;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            touch-action: manipulation;
            min-height: 44px;
            min-width: 44px;
        }
        @media (min-width: 640px) {
            .control-btn {
                padding: 14px 28px;
                font-size: 1rem;
                border-radius: 18px;
            }
        }
        @media (min-width: 1024px) {
            .control-btn {
                padding: 16px 32px;
                font-size: 1.125rem;
                border-radius: 20px;
            }
        }

        .control-btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: all 0.5s ease;
        }

        .control-btn:hover::before {
            width: 300px;
            height: 300px;
        }

        .btn-primary {
            background: #8b5cf6;
            color: white;
            border: none;
            box-shadow: 0 2px 8px rgba(139, 92, 246, 0.3);
        }
        .btn-primary:active {
            background: #7c3aed;
            transform: scale(0.98);
        }
        @media (min-width: 1024px) {
            .btn-primary:hover {
                background: #7c3aed;
                box-shadow: 0 4px 12px rgba(139, 92, 246, 0.4);
            }
        }

        .btn-secondary {
            background: #2d2d3a;
            color: white;
            border: 1px solid #3d3d4a;
        }
        .btn-secondary:active {
            background: #3d3d4a;
            transform: scale(0.98);
        }
        @media (min-width: 1024px) {
            .btn-secondary:hover {
                background: #3d3d4a;
                border-color: #4d4d5a;
            }
        }

        .btn-success {
            background: linear-gradient(135deg, #00b09b, #96c93d);
            color: white;
        }

        .notification {
            position: fixed;
            top: 20px;
            right: 16px;
            left: 16px;
            padding: 16px 20px;
            border-radius: 16px;
            color: white;
            font-weight: 600;
            z-index: 10000;
            transform: translateX(400px);
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            max-width: 100%;
            word-wrap: break-word;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        @media (min-width: 640px) {
            .notification {
                top: 24px;
                right: 24px;
                left: auto;
                padding: 18px 24px;
                max-width: 400px;
                border-radius: 18px;
            }
        }
        @media (min-width: 1024px) {
            .notification {
                top: 30px;
                right: 30px;
                padding: 20px 30px;
                border-radius: 20px;
            }
        }

        .notification.show {
            transform: translateX(0);
        }

        .notification.success {
            background: #22c55e;
            border-color: #16a34a;
        }

        .notification.error {
            background: #ef4444;
            border-color: #dc2626;
        }

        .notification.info {
            background: #8b5cf6;
            border-color: #7c3aed;
        }

        .notification.warning {
            background: #f59e0b;
            border-color: #d97706;
        }

        .notification-close {
            background: none;
            border: none;
            color: white;
            font-size: 1.2em;
            cursor: pointer;
            margin-right: 10px;
            padding: 0;
            width: 25px;
            height: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Modal Styles */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            z-index: 1000;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal-content {
            background: #1a1a24;
            border: 2px solid #2d2d3a;
            border-radius: 0;
            padding: 24px;
            max-width: 90%;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
            position: relative;
            animation: modalAppear 0.2s ease;
            box-shadow: 
                0 20px 60px rgba(0, 0, 0, 0.8),
                0 0 0 1px rgba(139, 92, 246, 0.1);
        }
        @media (min-width: 768px) {
            .modal-content {
                border-radius: 8px;
                padding: 32px;
            }
        }
        @media (min-width: 640px) {
            .modal-content {
                padding: 32px;
                border-radius: 28px;
                max-width: 600px;
            }
        }
        @media (min-width: 1024px) {
            .modal-content {
                padding: 40px;
                border-radius: 32px;
            }
        }

        @keyframes modalAppear {
            from { opacity: 0; transform: scale(0.8); }
            to { opacity: 1; transform: scale(1); }
        }

        .modal-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .modal-title {
            font-size: 1.5rem;
            color: #fff;
            margin-bottom: 16px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: -0.5px;
        }
        @media (min-width: 640px) {
            .modal-title {
                font-size: 1.75rem;
            }
        }
        @media (min-width: 1024px) {
            .modal-title {
                font-size: 2rem;
            }
        }

        .question-text {
            font-size: 1.125rem;
            line-height: 1.7;
            margin-bottom: 24px;
            text-align: center;
            color: rgba(255, 255, 255, 0.95);
            font-weight: 500;
        }
        @media (min-width: 640px) {
            .question-text {
                font-size: 1.25rem;
                margin-bottom: 28px;
            }
        }
        @media (min-width: 1024px) {
            .question-text {
                font-size: 1.375rem;
                margin-bottom: 32px;
            }
        }

        .options-container {
            display: grid;
            grid-template-columns: 1fr;
            gap: 15px;
            margin: 30px 0;
        }

        .option-button {
            padding: 16px 20px;
            background: #1a1a24;
            border: 1px solid #2d2d3a;
            border-radius: 0;
            color: white;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.15s ease;
            text-align: right;
            position: relative;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            touch-action: manipulation;
            min-height: 56px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        @media (min-width: 768px) {
            .option-button {
                border-radius: 4px;
            }
        }
        @media (min-width: 640px) {
            .option-button {
                padding: 18px 24px;
                font-size: 1.125rem;
                border-radius: 18px;
            }
        }
        @media (min-width: 1024px) {
            .option-button {
                padding: 20px 28px;
                font-size: 1.25rem;
                border-radius: 20px;
            }
        }

        .option-button:hover,
        .option-button:active {
            background: #222230;
            border-color: #8b5cf6;
        }
        @media (min-width: 1024px) {
            .option-button:hover {
                transform: translateX(-4px);
            }
        }

        .option-button.selected {
            background: #2a1a3d;
            border-color: #8b5cf6;
            box-shadow: inset 0 0 20px rgba(139, 92, 246, 0.2);
        }

        .option-button.correct {
            background: #1a2a1a;
            border-color: #22c55e;
            box-shadow: inset 0 0 20px rgba(34, 197, 94, 0.2);
        }

        .option-button.incorrect {
            background: #2a1a1a;
            border-color: #ef4444;
            box-shadow: inset 0 0 20px rgba(239, 68, 68, 0.2);
        }

        .option-label {
            display: inline-block;
            width: 30px;
            height: 30px;
            background: #00d2ff;
            color: white;
            border-radius: 50%;
            text-align: center;
            line-height: 30px;
            margin-left: 15px;
            font-weight: bold;
        }

        .submit-button {
            width: 100%;
            padding: 16px;
            background: #8b5cf6;
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1.125rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.15s ease;
            margin-top: 24px;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            touch-action: manipulation;
            min-height: 52px;
            box-shadow: 0 2px 8px rgba(139, 92, 246, 0.3);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        @media (min-width: 640px) {
            .submit-button {
                padding: 18px;
                font-size: 1.25rem;
                border-radius: 14px;
            }
        }
        @media (min-width: 1024px) {
            .submit-button {
                padding: 20px;
                font-size: 1.375rem;
                border-radius: 16px;
            }
        }

        .submit-button:active:not(:disabled) {
            background: #7c3aed;
            transform: scale(0.98);
        }
        @media (min-width: 1024px) {
            .submit-button:hover:not(:disabled) {
                background: #7c3aed;
                box-shadow: 0 4px 12px rgba(139, 92, 246, 0.4);
            }
        }

        .submit-button:disabled {
            background: rgba(255, 255, 255, 0.1);
            cursor: not-allowed;
        }

        .feedback-section {
            margin-top: 25px;
            padding: 20px;
            border-radius: 15px;
            text-align: center;
            font-weight: 600;
            display: none;
        }

        .feedback-correct {
            background: rgba(0, 176, 155, 0.2);
            border: 2px solid #00b09b;
            color: #00b09b;
        }

        .feedback-incorrect {
            background: rgba(255, 65, 108, 0.2);
            border: 2px solid #ff416c;
            color: #ff416c;
        }

        .speed-round-board {
            background: rgba(248, 87, 166, 0.1);
            border: 2px solid rgba(248, 87, 166, 0.3);
            border-radius: 20px;
            padding: 20px;
            margin: 20px 0;
        }

        .replace-cell {
            position: relative;
        }

        .replace-cell.selectable {
            cursor: pointer;
            border-color: #f857a6;
        }

        .replace-cell.selectable:hover {
            background: rgba(248, 87, 166, 0.2);
            transform: scale(1.1);
        }

        .replace-overlay {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 2em;
            opacity: 0;
            transition: all 0.3s ease;
        }

        .replace-cell.selectable:hover .replace-overlay {
            opacity: 1;
        }

        .replace-cell.selected {
            background: rgba(248, 87, 166, 0.3);
            border-color: #f857a6;
            transform: scale(1.05);
        }

        .free-placement-modal .game-board {
            margin: 20px 0;
        }

        .free-placement-modal .board-cell {
            cursor: pointer;
        }

        .free-placement-modal .board-cell:hover {
            background: rgba(0, 210, 255, 0.2);
            border-color: #00d2ff;
        }

        .countdown-timer {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 5em;
            font-weight: 900;
            color: #00d2ff;
            z-index: 10001;
            display: none;
            animation: countdownPulse 1s ease infinite;
        }

        @keyframes countdownPulse {
            0%, 100% { transform: translate(-50%, -50%) scale(1); }
            50% { transform: translate(-50%, -50%) scale(1.2); }
        }

        /* تحسينات للهواتف */
        @media (max-width: 768px) {
            .game-controls {
                flex-direction: column;
                align-items: stretch;
                gap: 12px;
            }
            .game-controls .control-btn {
                width: 100%;
            }

            .power-ups-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
            }

            .power-ups-section {
                padding: 20px 16px;
                margin: 20px 0;
            }
        }

        @media (max-width: 480px) {
            .power-ups-grid {
                grid-template-columns: 1fr;
                gap: 10px;
            }

            .power-ups-section {
                padding: 16px 12px;
            }
        }

        /* أنماط التحميل */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 10002;
            backdrop-filter: blur(5px);
        }

        .loading-spinner {
            width: 80px;
            height: 80px;
            border: 5px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #00d2ff;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .loading-text {
            color: white;
            margin-top: 20px;
            font-size: 1.2em;
            text-align: center;
        }

        .btn-loading {
            position: relative;
            color: transparent !important;
        }

        .btn-loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            top: 50%;
            left: 50%;
            margin-left: -10px;
            margin-top: -10px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 0.8s ease-in-out infinite;
        }

        /* تحسينات اللمس */
        @media (hover: none) {
            .board-cell:hover:not(.disabled) {
                transform: none;
            }
            
            .power-up-card:hover:not(.used):not(.unaffordable) {
                transform: none;
            }
            
            .option-button:hover {
                transform: none;
            }
        }

        /* تحسينات إضافية للهواتف */
        .mobile-optimized {
            -webkit-overflow-scrolling: touch;
        }

        .no-select {
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            -khtml-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }
    </style>
</head>
<body class="mobile-optimized">
    <!-- شاشة التحميل -->
    <div class="loading-overlay" id="globalLoading">
        <div style="text-align: center;">
            <div class="loading-spinner"></div>
            <div class="loading-text" id="loadingText">جاري التحميل...</div>
        </div>
    </div>

    <div class="game-wrapper">
        <div class="game-container">
            <div class="game-header">
                <h1 class="game-title">🎮 XO الذكية</h1>
                <div class="game-mode">
                    Game ID: {{ $game->id }} | 
                    @if($game->game_type === 'computer')
                        ضد الكمبيوتر
                    @elseif($game->game_type === 'online')
                        ضد لاعب
                    @else
                        بطولة
                    @endif
                </div>
            </div>

            <div class="players-arena">
                <!-- Player 1 -->
                <div class="player-card" id="player1">
                    <div class="player-avatar">
                        {{ substr($game->player1->user->name, 0, 1) }}
                    </div>
                    <div class="player-name">{{ $game->player1->user->name }}</div>
                    <div class="player-symbol">X</div>
                    <div class="player-stats">
                        <div class="stat-item">
                            <div class="stat-value" id="player1Points">{{ $game->player1->points }}</div>
                            <div class="stat-label">نقاط</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value">{{ $game->player1->games_played }}</div>
                            <div class="stat-label">ألعاب</div>
                        </div>
                    </div>
                </div>

                <!-- Game Board Section -->
                <div class="game-board-section">
                    <div class="board-wrapper">
                        <div class="game-board" id="gameBoard">
                            @for($i = 0; $i < 9; $i++)
                            <div class="board-cell no-select" data-position="{{ $i }}" onclick="makeMove({{ $i }})">
                                {{ $game->getBoardArrayAttribute()[$i] ?? '' }}
                            </div>
                            @endfor
                        </div>
                    </div>

                    <div class="computer-thinking" id="computerThinking">
                        <div class="thinking-animation">
                            <span>الكمبيوتر يفكر</span>
                            <div class="thinking-dots">
                                <div class="thinking-dot"></div>
                                <div class="thinking-dot"></div>
                                <div class="thinking-dot"></div>
                            </div>
                        </div>
                    </div>

                    <div class="game-status" id="gameStatus"></div>
                </div>

                <!-- Player 2 -->
                <div class="player-card" id="player2">
                    <div class="player-avatar">
                        @if($game->player2 && $game->player2->user)
                            {{ substr($game->player2->user->name, 0, 1) }}
                        @else
                            🤖
                        @endif
                    </div>
                    <div class="player-name">
                        @if($game->player2 && $game->player2->user)
                            {{ $game->player2->user->name }}
                        @else
                            الكمبيوتر
                        @endif
                    </div>
                    <div class="player-symbol">O</div>
                    <div class="player-stats">
                        <div class="stat-item">
                            <div class="stat-value">
                                @if($game->player2 && $game->player2->user)
                                    {{ $game->player2->points }}
                                @else
                                    AI
                                @endif
                            </div>
                            <div class="stat-label">نقاط</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value">
                                @if($game->player2 && $game->player2->user)
                                    {{ $game->player2->games_played }}
                                @else
                                    ∞
                                @endif
                            </div>
                            <div class="stat-label">ألعاب</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Speed Round Indicator -->
            <div class="speed-round-indicator" id="speedRound">
                <div class="speed-round-title">🎯 جولة السرعة!</div>
                <p>أول من يجب بشكل صحيح يمكنه استبدال أي مربع للخصم!</p>
            </div>

            <!-- Power Ups Section -->
            <div class="power-ups-section">
                <div class="power-ups-title">🎴 البطاقات الخاصة</div>
                <div class="power-ups-grid" id="powerUpsGrid">
                    <!-- سيتم ملؤها بالجافاسكريبت -->
                </div>
            </div>

            <!-- Game Controls -->
            <div class="game-controls">
                <button class="control-btn btn-secondary" onclick="confirmExit()">
                    <i class="fas fa-home"></i> الرئيسية
                </button>
                <button class="control-btn btn-primary" id="refreshBtn" onclick="forceRefresh()">
                    <i class="fas fa-sync-alt"></i> تحديث
                </button>
                <button class="control-btn btn-success" id="restartBtn" onclick="restartGame()" style="display: none;">
                    <i class="fas fa-redo"></i> لعبة جديدة
                </button>
            </div>
        </div>
    </div>

    {{-- مودال تأكيد الخروج --}}
    <div class="modal-overlay" id="exitModal" style="display:none;">
        <div class="modal-content" style="max-width:400px;text-align:center;">
            <h2 class="modal-title">⚠️ تأكيد الخروج</h2>
            <p class="question-text">
                إذا خرجت الآن تُحسب <strong>خاسراً</strong> بدون نقاط،<br>
                وسيُمنح الخصم <strong>20 نقطة</strong>.<br>
                متأكد من الخروج؟
            </p>
            <div style="display:flex;gap:15px;justify-content:center;margin-top:20px;">
                <button class="control-btn btn-secondary" onclick="closeExitModal()">
                    <i class="fas fa-times"></i> إلغاء
                </button>
                <button class="control-btn btn-primary" onclick="proceedExit()">
                    <i class="fas fa-check"></i> نعم، اخرج
                </button>
            </div>
        </div>
    </div>

    <!-- Question Modal -->
    <div class="modal-overlay" id="questionModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">🧠 سؤال الذكاء</h2>
                <div class="question-meta" style="display: flex; justify-content: center; gap: 20px; margin-top: 10px;">
                    <div class="question-category" id="questionCategory"></div>
                    <div class="question-difficulty" id="questionDifficulty"></div>
                </div>
            </div>
            
            <div class="question-text" id="questionText"></div>
            
            <div class="options-container" id="optionsContainer"></div>
            
            <button class="submit-button" id="submitBtn" onclick="submitAnswer()">تأكيد الإجابة</button>
            
            <div class="feedback-section" id="answerFeedback"></div>
        </div>
    </div>

    <!-- Speed Round Replace Modal -->
    <div class="modal-overlay" id="replaceModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">🎯 جولة السرعة - استبدال مربع</h2>
                <p>أنت الفائز في جولة السرعة! اختر مربعاً للخصم لاستبداله بعلامتك</p>
            </div>
            
            <div class="speed-round-board">
                <div class="game-board" id="replaceBoard"></div>
            </div>
            
            <div style="display: flex; justify-content: center; gap: 20px; margin-top: 20px;">
                <button class="control-btn btn-secondary" onclick="skipReplace()">
                    <i class="fas fa-times"></i> تخطي
                </button>
                <button class="control-btn btn-primary" onclick="confirmReplace()">
                    <i class="fas fa-check"></i> تأكيد الاستبدال
                </button>
            </div>
        </div>
    </div>

    <!-- Free Placement Modal -->
    <div class="modal-overlay" id="freePlacementModal">
        <div class="modal-content free-placement-modal">
            <div class="modal-header">
                <h2 class="modal-title">🎯 الوضع الحر</h2>
                <p>اختر أي مربع لوضع علامتك (حتى المربعات المحجوزة)</p>
            </div>
            
            <div class="game-board" id="freePlacementBoard">
                <!-- سيتم ملؤها بالجافاسكريبت -->
            </div>
            
            <div style="display: flex; justify-content: center; gap: 20px; margin-top: 20px;">
                <button class="control-btn btn-secondary" onclick="closeFreePlacement()">
                    <i class="fas fa-times"></i> إلغاء
                </button>
            </div>
        </div>
    </div>

    <!-- Countdown Timer -->
    <div class="countdown-timer" id="countdownTimer"></div>

    <script>
        // ==================== تهيئة المتغيرات العالمية ====================
        
        // Game State
        const gameState = {
            gameId: {{ $game->id }},
            board: @json($game->getBoardArrayAttribute() ?? array_fill(0, 9, null)),
            currentTurn: '{{ $game->current_turn ?? 'player1' }}',
            gameStatus: '{{ $game->status === 'completed' ? ($game->winner ? ($game->winner === 'X' ? 'win' : 'lose') : 'draw') : 'active' }}',
            isPlayerTurn: {{ ($game->isPlayerTurn($player->id ?? Auth::user()->player?->id ?? 0) ?? false) ? 'true' : 'false' }},
            isAgainstComputer: {{ ($game->game_type === 'computer' && !$game->player2_id) ? 'true' : 'false' }},
            speedRoundActive: {{ $game->speed_round_activated ? 'true' : 'false' }},
            winner: '{{ $game->winner ?? '' }}',
            player1_id: {{ $game->player1_id ?? 'null' }},
            player2_id: {{ $game->player2_id ?? 'null' }},
            speedRoundTriggered: false,
            selectedPosition: null,
            selectedOption: null,
            currentQuestion: null,
            showFeedback: false,
            playerPoints: {{ $player->points ?? Auth::user()->player?->points ?? 0 }}
        };

        // التأكد من أن board مصفوفة
        if (!Array.isArray(gameState.board)) {
            console.warn('⚠️ تهيئة gameState.board كصفيفية افتراضية');
            gameState.board = Array(9).fill(null);
        }

        // المتغيرات العامة
        let autoRefreshInterval = null;
        let speedRoundActive = {{ $game->speed_round_activated ? 'true' : 'false' }};
        let speedRoundUsed = {{ $game->speed_round_used ? 'true' : 'false' }};
        let speedRoundWinner = false;
        let selectedReplacePosition = null;
        let gameChannel = null;
        let computerMoveInProgress = false; 
        const usedQuestions = new Set();
        const currentPlayerId = {{ $player->id ?? Auth::user()->player?->id ?? 0 }};
        let countdownInterval = null; 
        let countdownStarted  = false;
        let previousRemainingCells = 9;
        let lastUpdateTime = 0;
        const UPDATE_COOLDOWN = 2000;
        let errorCount = 0;
        const MAX_ERRORS = 5;
        
        // حالة البطاقات
        let playerPowerUps = {};
        let selectedPowerUp = null;

        let computerMoveTimeout = null;
        let computerMoveAttempts = 0;
        const MAX_COMPUTER_ATTEMPTS = 3;
        // نظام الصوتيات
        const audioContext = new (window.AudioContext || window.webkitAudioContext)();
        const sounds = {
            correct: () => playSound(523.25, 0.2, 'sine', 0.3),
            incorrect: () => playSound(349.23, 0.3, 'sawtooth', 0.2),
            win: () => playSound(659.25, 0.5, 'sine', 0.8),
            lose: () => playSound(293.66, 0.5, 'square', 0.4),
            move: () => playSound(392, 0.1, 'sine', 0.1),
            notification: () => playSound(784, 0.2, 'triangle', 0.2),
            powerup: () => playSound(622.25, 0.3, 'sine', 0.4)
        };

        // نظام التعافي المحلي
        const STORAGE_KEY = `game_${gameState.gameId}_backup`;
        let isOnline = navigator.onLine;

        // ==================== نظام الصوتيات ====================

        function playSound(frequency, duration, type = 'sine', volume = 0.1) {
            try {
                if (audioContext.state === 'suspended') {
                    audioContext.resume();
                }

                const oscillator = audioContext.createOscillator();
                const gainNode = audioContext.createGain();
                
                oscillator.connect(gainNode);
                gainNode.connect(audioContext.destination);
                
                oscillator.frequency.value = frequency;
                oscillator.type = type;
                
                gainNode.gain.value = volume;
                gainNode.gain.exponentialRampToValueAtTime(0.001, audioContext.currentTime + duration);
                
                oscillator.start(audioContext.currentTime);
                oscillator.stop(audioContext.currentTime + duration);
            } catch (error) {
                console.warn('❌ لا يمكن تشغيل الصوت:', error);
            }
        }

        // ==================== نظام التعافي المحلي ====================

        function saveGameState() {
            try {
                const backup = {
                    gameState: gameState,
                    playerPowerUps: playerPowerUps,
                    speedRoundActive: speedRoundActive,
                    speedRoundUsed: speedRoundUsed,
                    timestamp: Date.now()
                };
                localStorage.setItem(STORAGE_KEY, JSON.stringify(backup));
                console.log('💾 تم حفظ حالة اللعبة محلياً');
            } catch (error) {
                console.warn('⚠️ لا يمكن حفظ حالة اللعبة محلياً:', error);
            }
        }

        function loadGameState() {
            try {
                const backup = localStorage.getItem(STORAGE_KEY);
                if (backup) {
                    const data = JSON.parse(backup);
                    
                    // التحقق من أن البيانات حديثة (أقل من 5 دقائق)
                    if (Date.now() - data.timestamp < 5 * 60 * 1000) {
                        Object.assign(gameState, data.gameState);
                        playerPowerUps = data.playerPowerUps || {};
                        speedRoundActive = data.speedRoundActive || false;
                        speedRoundUsed = data.speedRoundUsed || false;
                        
                        console.log('🔄 تم استعادة حالة اللعبة من النسخة الاحتياطية');
                        return true;
                    } else {
                        localStorage.removeItem(STORAGE_KEY);
                    }
                }
            } catch (error) {
                console.warn('⚠️ لا يمكن تحميل حالة اللعبة المحلية:', error);
            }
            return false;
        }

        function clearBackup() {
            try {
                localStorage.removeItem(STORAGE_KEY);
            } catch (error) {
                console.warn('⚠️ لا يمكن مسح النسخة الاحتياطية:', error);
            }
        }
        
        // ==================== نظام التحميل ====================

        function showLoading(text = 'جاري التحميل...') {
            document.getElementById('loadingText').textContent = text;
            document.getElementById('globalLoading').style.display = 'flex';
        }

        function hideLoading() {
            document.getElementById('globalLoading').style.display = 'none';
        }

        function setButtonLoading(button, isLoading) {
            if (isLoading) {
                button.classList.add('btn-loading');
                button.disabled = true;
            } else {
                button.classList.remove('btn-loading');
                button.disabled = false;
            }
        }

        // ==================== نظام البطاقات مع التكاليف ====================
        
        // استبدال دالة loadPowerUps بدالة أكثر قوة
        async function loadPowerUps() {
            try {
                console.log('🔄 جاري تحميل البطاقات...');
                
                // محاولة الاتصال بالسيرفر
                const response = await fetch(`/game/${gameState.gameId}/powerups`, {
                    timeout: 5000 // وقت انتظار 5 ثواني
                });
                
                if (response.ok) {
                    const data = await response.json();
                    playerPowerUps = data.powerUps || getDefaultPowerUps();
                    console.log('✅ تم تحميل البطاقات من السيرفر:', playerPowerUps);
                } else {
                    // استخدام البطاقات الافتراضية كبديل
                    console.log('🎴 استخدام البطاقات الافتراضية');
                    playerPowerUps = getDefaultPowerUps();
                }
                
            } catch (error) {
                console.error('❌ خطأ في تحميل البطاقات:', error);
                playerPowerUps = getDefaultPowerUps();
            }
            
            renderPowerUps();
        }

        // إضافة هذه الدالة للتعامل مع الأخطاء
        function emergencyPowerUpSystem() {
            console.log('🚨 تفعيل نظام الطوارئ للبطاقات');
            
            // تأكد من وجود البطاقات الافتراضية
            if (!playerPowerUps || Object.keys(playerPowerUps).length === 0) {
                playerPowerUps = getDefaultPowerUps();
            }
            
            // تأكد من عرض البطاقات
            renderPowerUps();
            
            // إشعار المستخدم
            showNotification('🔧 نظام البطاقات يعمل في الوضع الافتراضي', 'info');
        }

        // البطاقات الافتراضية مع التكاليف
        function getDefaultPowerUps() {
            return {
                'double_move': {
                    'name': 'حركة مزدوجة', 
                    'used': false, 
                    'icon': '⚡',
                    'cost': 10,
                    'description': 'العب مرتين متتاليتين'
                },
                'block_opponent': {
                    'name': 'حجب الخصم', 
                    'used': false, 
                    'icon': '🚫',
                    'cost': 5,
                    'description': 'احجب دور الخصم التالي'
                },
                'free_placement': {
                    'name': 'وضع حر', 
                    'used': false, 
                    'icon': '🎯',
                    'cost': 10,
                    'description': 'ضع علامتك في أي مكان'
                },
                'shuffle_board': {
                    'name': 'تبديل اللوحة', 
                    'used': false, 
                    'icon': '🔀',
                    'cost': 7,
                    'description': 'بدل مواقع العلامات'
                }
            };
        }
        
        // عرض البطاقات
        function renderPowerUps() {
            const grid = document.getElementById('powerUpsGrid');
            if (!grid) {
                console.warn('⚠️ Power-ups grid not found');
                return;
            }
            
            grid.innerHTML = '';
            
            if (!playerPowerUps || Object.keys(playerPowerUps).length === 0) {
                console.warn('⚠️ No power-ups available, loading defaults');
                playerPowerUps = getDefaultPowerUps();
            }
            
            Object.entries(playerPowerUps).forEach(([key, powerUp]) => {
                const canAfford = (gameState.playerPoints >= powerUp.cost);
                const isClickable = !powerUp.used && gameState.isPlayerTurn && gameState.gameStatus === 'active' && canAfford;
                const costClass = canAfford ? 'affordable' : 'unaffordable';
                const cardClass = `power-up-card ${powerUp.used ? 'used' : ''} ${!canAfford && !powerUp.used ? 'unaffordable' : ''} ${isClickable ? 'clickable' : ''}`;
                
                const card = document.createElement('div');
                card.className = cardClass;
                card.setAttribute('data-powerup-key', key);
                card.innerHTML = `
                    <div class="power-up-icon">${powerUp.icon}</div>
                    <div class="power-up-name">${powerUp.name}</div>
                    <div class="power-up-cost ${costClass}">${powerUp.cost} نقاط</div>
                    <div class="power-up-desc">${getPowerUpDescription(key)}</div>
                    <br><br>
                    ${isClickable ? '<div class="power-up-badge">انقر للاستخدام</div>' : ''}
                `;
                
                if (isClickable) {
                    card.style.cursor = 'pointer';
                    card.onclick = (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        console.log('🎴 Clicked power-up:', key);
                        usePowerUp(key);
                    };
                    card.addEventListener('touchstart', (e) => {
                        e.preventDefault();
                        card.style.transform = 'scale(0.95)';
                    });
                    card.addEventListener('touchend', (e) => {
                        e.preventDefault();
                        card.style.transform = '';
                        usePowerUp(key);
                    });
                } else {
                    card.style.cursor = 'not-allowed';
                }
                
                grid.appendChild(card);
            });
            
            console.log('✅ Power-ups rendered:', Object.keys(playerPowerUps).length);
        }
        
        // أوصاف البطاقات
        function getPowerUpDescription(key) {
            const powerUp = playerPowerUps[key];
            if (powerUp) {
                return powerUp.description;
            }
            
            const descriptions = {
                'double_move': 'العب مرتين متتاليتين',
                'block_opponent': 'احجب دور الخصم التالي', 
                'free_placement': 'ضع علامتك في أي مكان',
                'shuffle_board': 'بدل مواقع العلامات'
            };
            return descriptions[key] || '';
        }
        
        // استخدام البطاقة
        async function usePowerUp(powerUpKey) {
            console.log('🎴 استخدام البطاقة:', powerUpKey);
            
            // ✅ إيقاف الكمبيوتر مؤقتاً أثناء استخدام البطاقة
            const wasComputerMoving = computerMoveInProgress;
            if (wasComputerMoving) {
                console.log('⏸️ إيقاف الكمبيوتر مؤقتاً لاستخدام البطاقة');
                resetComputerState();
            }

            // ✅ تحديث حالة اللعبة أولاً للتأكد من التزامن
            await checkGameState();
            
            const powerUp = playerPowerUps[powerUpKey];
            if (!powerUp) {
                showNotification('❌ البطاقة غير متوفرة', 'error');
                emergencyPowerUpSystem(); // تفعيل نظام الطوارئ
                return;
            }
            
            if (powerUp.used) {
                showNotification('❌ هذه البطاقة مستخدمة مسبقاً', 'error');
                return;
            }
            
            // ✅ التحقق من رصيد النقاط
            if (gameState.playerPoints < powerUp.cost) {
                showNotification(`❌ نقاطك غير كافية! تحتاج ${powerUp.cost} نقاط`, 'error');
                return;
            }
            
            if (!gameState.isPlayerTurn) {
                showNotification('❌ ليس دورك لاستخدام البطاقة', 'error');
                return;
            }
            
            // ✅ تأكيد الاستخدام مع التكلفة
            const confirmMessage = `هل تريد استخدام "${powerUp.name}" بتكلفة ${powerUp.cost} نقطة؟`;
            if (!confirm(confirmMessage)) {
                return;
            }
            
            selectedPowerUp = powerUpKey;
            
            // معالجة خاصة للوضع الحر
            if (powerUpKey === 'free_placement') {
                showFreePlacementModal();
                return;
            }
            
            // استخدام البطاقات الأخرى مباشرة
            await sendPowerUpRequest(powerUpKey);

            // ✅ إعادة تشغيل الكمبيوتر إذا كان يلعب سابقاً
            if (wasComputerMoving && gameState.isAgainstComputer && !gameState.isPlayerTurn) {
                console.log('🔄 إعادة تشغيل الكمبيوتر بعد استخدام البطاقة');
                setTimeout(() => {
                    if (shouldComputerMove()) {
                        startComputerMove();
                    }
                }, 1000);
            }
        }
        
        setTimeout(() => {
            if (!playerPowerUps || Object.keys(playerPowerUps).length === 0) {
                emergencyPowerUpSystem();
            }
        }, 3000);

        // إرسال طلب استخدام البطاقة مع معالجة الأخطاء
        async function sendPowerUpRequest(powerUpKey, position = null) {
            showLoading('جاري استخدام البطاقة...');
            
            try {
                const powerUp = playerPowerUps[powerUpKey];
                const payload = { 
                    power_up: powerUpKey,
                    cost: powerUp.cost
                };
                
                if (position !== null) {
                    payload.position = position;
                }
                
                console.log('🎴 إرسال طلب استخدام البطاقة:', payload);
                
                const response = await fetch(`/game/${gameState.gameId}/use-powerup`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(payload)
                });
                
                    if (response.ok) {
                        const result = await response.json();
                        showNotification(`✅ ${result.message} - تم خصم ${powerUp.cost} نقاط`, 'success');
                        sounds.powerup();
                        
                        // ✅ تحديث البطاقات والنقاط
                        if (result.powerUps) {
                            playerPowerUps = result.powerUps;
                        } else {
                            playerPowerUps[powerUpKey].used = true;
                        }
                        
                        // ✅ تحديث النقاط
                        if (result.player_points !== undefined) {
                            gameState.playerPoints = result.player_points;
                            updatePlayerPointsDisplay();
                        }
                        
                        renderPowerUps();
                        
                        // ✅ تحديث حالة اللعبة مع التحقق من الفوز - هذا اللي ناقص!
                        if (result.game) {
                            updateGameState(result.game);
                        } else {
                            await checkGameState(); // يجلب أحدث حالة من السيرفر
                        }
                        
                    } else if (response.status === 404) {
                        // ✅ محاكاة البطاقة محلياً مع التحقق من الفوز
                        console.log('🎴 محاكاة استخدام البطاقة محلياً');
                        simulatePowerUpEffect(powerUpKey, position);
                    } else {
                        const error = await response.json();
                        showNotification('❌ ' + (error.error || 'حدث خطأ'), 'error');
                        await checkGameState();
                    }
            } catch (error) {
                console.error('❌ Error using power up:', error);
                showNotification('❌ حدث خطأ في استخدام البطاقة', 'error');
                
                // ✅ المحاولة المحلية كبديل
                simulatePowerUpEffect(powerUpKey, position);
            } finally {
                hideLoading();
            }
        }
        function checkWinAfterPowerUp() {
            const winner = checkWinnerLocally();
            if (winner) {
                const currentPlayerId = {{ $player->id ?? Auth::user()->player?->id ?? 0 }};
                const isCurrentPlayerWinner = (winner === 'X' && gameState.player1_id === currentPlayerId) ||
                                            (winner === 'O' && gameState.player2_id === currentPlayerId);
                
                gameState.winner = winner;
                gameState.gameStatus = isCurrentPlayerWinner ? 'win' : 'lose';
                updateGameStatus();
                stopAutoRefresh();
                return true;
            }
            
            if (gameState.board.every(cell => cell !== null) && !winner) {
                gameState.gameStatus = 'draw';
                updateGameStatus();
                stopAutoRefresh();
                return true;
            }
            
            return false;
        }
        // محاكاة تأثير البطاقة محلياً مع خصم النقاط
        function simulatePowerUpEffect(powerUpKey, position) {
            const powerUp = playerPowerUps[powerUpKey];
            if (!powerUp) {
                console.error('❌ البطاقة غير موجودة:', powerUpKey);
                return;
            }
            
            // ✅ محاكاة خصم النقاط محلياً
            gameState.playerPoints -= powerUp.cost;
            playerPowerUps[powerUpKey].used = true;
            
            updatePlayerPointsDisplay();
            renderPowerUps();
            
            let message = '';
            let gameEnded = false;
            
            switch(powerUpKey) {
                case 'double_move':
                    message = `🎯 تم تفعيل الحركة المزدوجة! - تم خصم ${powerUp.cost} نقاط`;
                    break;
                    
                case 'block_opponent':
                    message = `🚫 تم حجب الخصم! - تم خصم ${powerUp.cost} نقاط`;
                    gameState.currentTurn = gameState.currentTurn === 'player1' ? 'player1' : 'player2';
                    updatePlayersTurn();
                    break;
                    
                case 'free_placement':
                    if (position !== null) {
                        const playerSymbol = gameState.currentTurn === 'player1' ? 'X' : 'O';
                        gameState.board[position] = playerSymbol;
                        updateBoard();
                        
                        // ✅ التحقق من الفوز فوراً بعد الوضع الحر
                        const winner = checkWinnerLocally();
                        if (winner) {
                            gameState.winner = winner;
                            const currentPlayerId = {{ $player->id ?? Auth::user()->player?->id ?? 0 }};
                            const isCurrentPlayerWinner = (winner === 'X' && gameState.player1_id === currentPlayerId) ||
                                                        (winner === 'O' && gameState.player2_id === currentPlayerId);
                            gameState.gameStatus = isCurrentPlayerWinner ? 'win' : 'lose';
                            gameEnded = true;
                            updateGameStatus();
                        } else if (gameState.board.every(cell => cell !== null)) {
                            gameState.gameStatus = 'draw';
                            gameEnded = true;
                            updateGameStatus();
                        } else {
                            gameState.currentTurn = gameState.currentTurn === 'player1' ? 'player2' : 'player1';
                            updatePlayersTurn();
                        }
                        message = `🎁 تم وضع علامتك! - تم خصم ${powerUp.cost} نقاط`;
                    }
                    break;
                    
                case 'shuffle_board':
                    shuffleBoardLocally();
                    // ✅ التحقق من الفوز بعد الخلط
                    const winner = checkWinnerLocally();
                    if (winner) {
                        gameState.winner = winner;
                        const currentPlayerId = {{ $player->id ?? Auth::user()->player?->id ?? 0 }};
                        const isCurrentPlayerWinner = (winner === 'X' && gameState.player1_id === currentPlayerId) ||
                                                    (winner === 'O' && gameState.player2_id === currentPlayerId);
                        gameState.gameStatus = isCurrentPlayerWinner ? 'win' : 'lose';
                        gameEnded = true;
                        updateGameStatus();
                    }
                    message = `🔀 تم تبديل اللوحة! - تم خصم ${powerUp.cost} نقاط`;
                    break;
            }
            
            showNotification('✅ ' + message, 'success');
            sounds.powerup();
            
            // ✅ إذا انتهت اللعبة، أوقف التحديث التلقائي
            if (gameEnded) {
                stopAutoRefresh();
            }
            
            saveGameState();
        }

        // خلط اللوحة محلياً
        function shuffleBoardLocally() {
            // التأكد من أن gameState.board موجودة
            if (!gameState.board || !Array.isArray(gameState.board)) {
                console.error('❌ gameState.board غير موجودة للخلط');
                gameState.board = Array(9).fill(null);
            }
            
            const board = [...gameState.board];
            const symbols = board.filter(cell => cell !== null);
            
            // خلط العلامات
            for (let i = symbols.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [symbols[i], symbols[j]] = [symbols[j], symbols[i]];
            }
            
            // إعادة بناء اللوحة
            let symbolIndex = 0;
            for (let i = 0; i < 9; i++) {
                if (board[i] !== null) {
                    gameState.board[i] = symbols[symbolIndex];
                    symbolIndex++;
                }
            }
            
            updateBoard();
            
            // ✅ التحقق من الفوز بعد الخلط
            updateWinStatusLocally();
            saveGameState();
        }

        function updateWinStatusLocally() {
            const winner = checkWinnerLocally();
            const isDraw = gameState.board.every(cell => cell !== null) && !winner;
            
            if (winner) {
                const currentPlayerId = {{ $player->id ?? Auth::user()->player?->id ?? 0 }};
                const isCurrentPlayerWinner = 
                    (winner === 'X' && gameState.player1_id === currentPlayerId) ||
                    (winner === 'O' && gameState.player2_id === currentPlayerId);
                
                gameState.winner = winner;
                gameState.gameStatus = isCurrentPlayerWinner ? 'win' : 'lose';
                
                // ✅ إظهار إشعار الفوز فوراً
                updateGameStatus();
                return true;
                
            } else if (isDraw) {
                gameState.gameStatus = 'draw';
                updateGameStatus();
                return true;
            }
            
            return false;
        }

        // تحديث عرض النقاط
        function updatePlayerPointsDisplay() {
            const playerPointsElement = document.getElementById('player1Points');
            if (playerPointsElement) {
                playerPointsElement.textContent = gameState.playerPoints;
            }
            console.log(`🔄 تم تحديث النقاط إلى: ${gameState.playerPoints}`);
        }

        // ==================== واجهة الوضع الحر ====================
        
        function showFreePlacementModal() {
            let boardHtml = '';
            for (let i = 0; i < 9; i++) {
                const cellValue = gameState.board[i] || '';
                boardHtml += `
                    <div class="board-cell no-select" data-position="${i}" onclick="selectFreePlacement(${i})">
                        ${cellValue}
                    </div>
                `;
            }
            
            document.getElementById('freePlacementBoard').innerHTML = boardHtml;
            document.getElementById('freePlacementModal').style.display = 'flex';
        }
        
        function closeFreePlacement() {
            document.getElementById('freePlacementModal').style.display = 'none';
            selectedPowerUp = null;
        }
        
        function selectFreePlacement(position) {
            if (selectedPowerUp === 'free_placement') {
                sendPowerUpRequest(selectedPowerUp, position);
                closeFreePlacement();
            }
        }

        // ==================== الدوال الأساسية ====================

        function makeMove(position) {
            // ✅ تحقق أفضل من أن gameState.board مصفوفة
            if (!gameState.board || typeof gameState.board !== 'object' || gameState.board.length === undefined) {
                console.error('❌ gameState.board غير صالحة:', gameState.board);
                showNotification('❌ خطأ في حالة اللعبة', 'error');
                return;
            }
            
            // ✅ تحويل إلى مصفوفة إذا لزم الأمر
            const boardArray = Array.isArray(gameState.board) ? gameState.board : Array.from(gameState.board);

            console.log('🎯 قبل الحركة:', {
                active: speedRoundActive,
                triggered: gameState.speedRoundTriggered,
                remaining: boardArray.filter(cell => cell === null).length
            });

            console.log('دورك؟', gameState.isPlayerTurn, 'الخلية:', boardArray[position], 'الحالة:', gameState.gameStatus);
            
            if (gameState.gameStatus !== 'active') {
                showNotification('❌ اللعبة منتهية', 'error');
                return;
            }

            // ✅ تحقق مزدوج من أن الخلية فارغة
            if (boardArray[position] !== null) {
                showNotification('❌ الخلية محجوزة مسبقاً', 'error');
                // تحديث اللوحة فوراً
                forceRefresh();
                return;
            }
            
            if (!gameState.isPlayerTurn) {
                showNotification('❌ ليس دورك للعب الآن', 'error');
                return;
            }

            gameState.selectedPosition = position;
            resetQuestionModal();
            fetchQuestion();
        }

        async function fetchQuestion() {
            showLoading('جاري تحميل السؤال...');
            
            try {
                const response = await fetch('/questions/random');
                if (!response.ok) throw new Error('Failed to fetch question');

                const question = await response.json();

                if (usedQuestions.has(question.id)) {
                    return fetchQuestion();
                }

                usedQuestions.add(question.id);
                showQuestionModal(question);

            } catch (error) {
                console.error('❌ Error fetching question:', error);
                const defaultQuestion = {
                    id: 'default',
                    question: 'ما هو ناتج 5 + 3؟',
                    options: ['8', '7', '6', '9'],
                    correct_option: '0',
                    category: 'رياضيات',
                    difficulty: 'easy'
                };
                showQuestionModal(defaultQuestion);
            } finally {
                hideLoading();
            }
        }

        function showQuestionModal(question) {
            gameState.currentQuestion = question;
            
            document.getElementById('questionText').textContent = question.question;
            document.getElementById('questionCategory').textContent = question.category;
            document.getElementById('questionDifficulty').textContent = getDifficultyText(question.difficulty);
            
            const optionsContainer = document.getElementById('optionsContainer');
            optionsContainer.innerHTML = '';
            
            question.options.forEach((option, index) => {
                const optionBtn = document.createElement('button');
                optionBtn.className = 'option-button no-select';
                optionBtn.innerHTML = `
                    ${option}
                    <span class="option-label">${getOptionLabel(index)}</span>
                `;
                optionBtn.onclick = () => selectOption(index);
                optionsContainer.appendChild(optionBtn);
            });
            
            document.getElementById('submitBtn').textContent = 'تأكيد الإجابة';
            document.getElementById('submitBtn').disabled = false;
            document.getElementById('answerFeedback').style.display = 'none';
            
            document.getElementById('questionModal').style.display = 'flex';
        }

        function selectOption(index) {
            if (gameState.showFeedback) return;
            
            document.querySelectorAll('.option-button').forEach(btn => {
                btn.classList.remove('selected');
            });
            
            const selectedBtn = document.querySelectorAll('.option-button')[index];
            selectedBtn.classList.add('selected');
            
            gameState.selectedOption = index.toString();
        }

        async function submitAnswer() {
            if (gameState.showFeedback) {
                document.getElementById('questionModal').style.display = 'none';
                
                if (speedRoundActive && gameState.selectedOption === gameState.currentQuestion.correct_option) {
                    speedRoundWinner = true;
                    showReplaceModal();
                } else {
                    await sendMoveToServer();
                }
                return;
            }

            if (!gameState.selectedOption) {
                showNotification('❌ يرجى اختيار إجابة', 'error');
                return;
            }

            gameState.showFeedback = true;
            const isCorrect = gameState.selectedOption === gameState.currentQuestion.correct_option;
            
            document.querySelectorAll('.option-button').forEach((btn, index) => {
                if (index.toString() === gameState.currentQuestion.correct_option) {
                    btn.classList.add('correct');
                } else if (index.toString() === gameState.selectedOption && !isCorrect) {
                    btn.classList.add('incorrect');
                }
            });
            
            const feedback = document.getElementById('answerFeedback');
            if (isCorrect) {
                feedback.className = 'feedback-section feedback-correct';
                
                if (speedRoundActive) {
                    feedback.innerHTML = `
                        <h4>🎉 إجابة صحيحة!</h4>
                        <p>🎯 لقد فزت بجولة السرعة! يمكنك الآن استبدال أي مربع للخصم</p>
                    `;
                    speedRoundWinner = true;
                    sounds.correct();
                } else {
                    feedback.innerHTML = `
                        <h4>🎉 إجابة صحيحة!</h4>
                        <p>تم وضع علامتك في الخلية</p>
                    `;
                    sounds.correct();
                }
                showNotification('🎉 إجابة صحيحة!', 'success');
            } else {
                feedback.className = 'feedback-section feedback-incorrect';
                const correctAnswer = gameState.currentQuestion.options[parseInt(gameState.currentQuestion.correct_option)];
                feedback.innerHTML = `
                    <h4>❌ إجابة خاطئة</h4>
                    <p>الإجابة الصحيحة هي: ${correctAnswer}</p>
                    <p>الدور ينتقل للخصم</p>
                `;
                speedRoundWinner = false;
                sounds.incorrect();
                showNotification('❌ إجابة خاطئة!', 'error');
            }
            feedback.style.display = 'block';
            
            if (speedRoundActive && isCorrect) {
                document.getElementById('submitBtn').textContent = 'استبدل مربع';
                document.getElementById('submitBtn').onclick = () => {
                    document.getElementById('questionModal').style.display = 'none';
                    showReplaceModal();
                };
            } else {
                document.getElementById('submitBtn').textContent = 'متابعة';
                document.getElementById('submitBtn').onclick = async () => {
                    document.getElementById('questionModal').style.display = 'none';
                    await sendMoveToServer();
                };
            }
        }

        async function sendMoveToServer() {
            showLoading('جاري إجراء الحركة...');
            
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            const payload = {
                position: gameState.selectedPosition,
                selected_option: gameState.selectedOption,
                correct_answer: gameState.selectedOption === gameState.currentQuestion?.correct_option
            };

            console.log('📤 Sending payload:', payload);

            try {
                const response = await fetch(`/game/${gameState.gameId}/move`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    console.error('❌ Server error:', errorData);
                    
                    if (errorData.error === 'الخلية محجوزة مسبقاً') {
                        // ✅ تحديث حالة اللعبة فوراً عند اكتشاف تضارب
                        showNotification('🔄 تحديث اللوحة بسبب تضارب البيانات...', 'info');
                        await forceRefresh();
                        return;
                    }
                    
                    throw new Error(errorData.error || 'خطأ غير معروف');
                }

                const result = await response.json();
                updateGameState(result);
                sounds.move();

            } catch (error) {
                console.error('❌ Error making move:', error);
                showNotification('حدث خطأ أثناء إجراء الحركة: ' + error.message, 'error');
            } finally {
                hideLoading();
            }
        }

        // ==================== تحديث اللعبة ====================

        function updateBoard() {
            const cells = document.querySelectorAll('.board-cell');
            
            // ✅ تحقق من أن gameState.board مصفوفة
            if (!gameState.board || typeof gameState.board !== 'object' || gameState.board.length === undefined) {
                console.error('❌ gameState.board غير صالحة في updateBoard:', gameState.board);
                return;
            }
            
            // ✅ تحويل إلى مصفوفة إذا لزم الأمر
            const boardArray = Array.isArray(gameState.board) ? gameState.board : Array.from(gameState.board);
            
            cells.forEach((cell, index) => {
                cell.textContent = boardArray[index] || '';
                
                cell.className = 'board-cell no-select';
                if (boardArray[index] === 'X') {
                    cell.classList.add('x');
                } else if (boardArray[index] === 'O') {
                    cell.classList.add('o');
                }
                
                if (boardArray[index] !== null || !gameState.isPlayerTurn || gameState.gameStatus !== 'active') {
                    cell.classList.add('disabled');
                } else {
                    cell.classList.remove('disabled');
                }
            });
        }

        function updatePlayersTurn() {
            const player1 = document.getElementById('player1');
            const player2 = document.getElementById('player2');
            
            player1.classList.remove('active');
            player2.classList.remove('active');
            
            if (gameState.currentTurn === 'player1') {
                player1.classList.add('active');
            } else {
                player2.classList.add('active');
            }
        }

        function updateGameStatus() {
            const gameStatusEl = document.getElementById('gameStatus');
            const restartBtn   = document.getElementById('restartBtn');

            if (gameState.gameStatus === 'active') {
                gameStatusEl.style.display = 'none';
                restartBtn.style.display = 'none';
                return;
            }

            // ✅ تحديد الفائز بشكل صحيح مع كل الحالات
            const currentPlayerId = {{ $player->id ?? Auth::user()->player?->id ?? 0 }};
            let title = '', msg = '';

            if (gameState.gameStatus === 'draw') {
                // تعادل
                title = '🤝 تعادل!';
                msg   = '+5 نقطة مضافة إلى رصيدك';
                gameStatusEl.className = 'game-status status-draw';
                sounds.notification();
            } else {
                // ✅ تحديد إذا كان اللاعب الحالي هو الفائز
                const isCurrentPlayerWinner = 
                    (gameState.winner === 'X' && gameState.player1_id === currentPlayerId) ||
                    (gameState.winner === 'O' && gameState.player2_id === currentPlayerId);

                if (isCurrentPlayerWinner) {
                    title = '🎉 مبروك! لقد فزت! 🎉';
                    msg   = '+20 نقطة مضافة إلى رصيدك';
                    gameStatusEl.className = 'game-status status-win';
                    sounds.win();
                } else {
                    title = '😔 خسارة! حاول مرة أخرى';
                    msg   = '+2 نقطة مضافة إلى رصيدك';
                    gameStatusEl.className = 'game-status status-lose';
                    sounds.lose();
                }
            }

            gameStatusEl.innerHTML = `<h2>${title}</h2><p>${msg}</p>`;
            gameStatusEl.style.display = 'block';
            restartBtn.style.display   = 'flex';
            
            // ✅ إظهار النقاط المحدثة
            updatePlayerPointsDisplay();
            
            startCountdown();
            stopAutoRefresh();
            
            // ✅ تنظيف النسخة الاحتياطية عند انتهاء اللعبة
            clearBackup();
        }

        function startCountdown() {
            if (countdownStarted) return;
            countdownStarted = true;

            if (countdownInterval) {
                clearInterval(countdownInterval);
                countdownInterval = null;
            }

            let sec = 5;
            const el = document.getElementById('countdownTimer');
            el.style.display = 'block';
            el.textContent   = sec;

            countdownInterval = setInterval(() => {
                sec--;
                el.textContent = sec;
                if (sec < 0) {
                    clearInterval(countdownInterval);
                    countdownInterval = null;
                    el.style.display = 'none';
                    window.location.href = '/dashboard';
                }
            }, 1000);
        }

        function updateGameState(gameData) {
            // التأكد من أن gameData تحتوي على البيانات المطلوبة
            if (!gameData) {
                console.error('❌ gameData غير موجودة');
                return;
            }

            console.log('🔄 تحديث حالة اللعبة:', gameData);
            
            // ✅ حفظ الحالة السابقة للتحقق من التغييرات
            const previousStatus = gameState.gameStatus;
            const previousWinner = gameState.winner;
            const wasPlayerTurn = gameState.isPlayerTurn;

            gameState.winner = gameData.winner || null;
            gameState.player1_id = gameData.player1_id;
            gameState.player2_id = gameData.player2_id;
            
            // ✅ إذا تغير الدور من اللاعب إلى الكمبيوتر
            if (wasPlayerTurn && !gameState.isPlayerTurn && gameState.isAgainstComputer) {
                console.log('🔄 تغيير الدور: اللاعب -> الكمبيوتر');
                
                // ✅ إعادة تعيين حالة الكمبيوتر أولاً
                resetComputerState();
                computerMoveAttempts = 0;
                
                // ✅ تأخير بسيط ثم بدء حركة الكمبيوتر
                setTimeout(() => {
                    if (shouldComputerMove()) {
                        console.log('🎯 بدء حركة الكمبيوتر بعد تغيير الدور');
                        startComputerMove();
                    }
                }, 800);
            }

            // ✅ تحديث اللوحة مع التحقق من الصحة
            if (gameData.board !== undefined && gameData.board !== null) {
                if (Array.isArray(gameData.board)) {
                    gameState.board = gameData.board;
                } else if (typeof gameData.board === 'object' && gameData.board.length !== undefined) {
                    gameState.board = Array.from(gameData.board);
                } else if (typeof gameData.board === 'string') {
                    try {
                        gameState.board = JSON.parse(gameData.board);
                    } catch (e) {
                        console.error('❌ خطأ في parse الـ board:', e);
                        gameState.board = Array(9).fill(null);
                    }
                } else {
                    console.warn('⚠️ نوع غير معروف لـ board، استخدام افتراضي');
                    gameState.board = Array(9).fill(null);
                }
            } else {
                console.warn('⚠️ board غير موجودة في gameData، استخدام افتراضي');
                gameState.board = Array(9).fill(null);
            }
            
            // ✅ تحديث حالة جولة السرعة من السيرفر
            if (gameData.speed_round_activated && !speedRoundActive) {
                speedRoundActive = true;
                gameState.speedRoundActive = true;
                gameState.speedRoundTriggered = true;
                document.getElementById('speedRound').style.display = 'block';
                sounds.notification();
            }

            gameState.currentTurn = gameData.current_turn;
            
            // ✅ تحديث حالة اللعبة بشكل صحيح
            if (gameData.status === 'completed') {
                if (gameData.winner) {
                    gameState.gameStatus = (gameData.winner === 'X' && gameState.player1_id === currentPlayerId) ||
                                        (gameData.winner === 'O' && gameState.player2_id === currentPlayerId) ? 'win' : 'lose';
                } else {
                    gameState.gameStatus = 'draw';
                }
            } else {
                gameState.gameStatus = 'active';
            }
            
            if (gameState.gameStatus !== 'active') {
                console.log('🛑 إيقاف الكمبيوتر - اللعبة انتهت');
                resetComputerState();
                computerMoveAttempts = 0;
            }
            
            gameState.isPlayerTurn = (
                (gameData.current_turn === 'player1' && gameData.player1_id === currentPlayerId) ||
                (gameData.current_turn === 'player2' && gameData.player2_id === currentPlayerId)
            );

            // ✅ تحديث النقاط إذا كانت متوفرة
            if (gameData.player_points !== undefined) {
                gameState.playerPoints = gameData.player_points;
                updatePlayerPointsDisplay();
            }

            updateBoard();
            updatePlayersTurn();
            updateGameStatus(); // ✅ التأكد من استدعاء updateGameStatus
            
            // ✅ التحقق من التغييرات في حالة الفوز
            if (previousStatus === 'active' && gameState.gameStatus !== 'active') {
                console.log('🎉 تغيير في حالة اللعبة:', previousStatus, '->', gameState.gameStatus);
            }
            
            // ✅ تحقق من جولة السرعة بعد كل تحديث
            checkSpeedRound();

            if (gameState.isAgainstComputer && !gameState.isPlayerTurn && gameState.gameStatus === 'active') {
                setTimeout(() => {
                    if (shouldComputerMove()) {
                        startComputerMove();
                    }
                }, 500);
            }
            
            // تحديث البطاقات
            loadPowerUps();
            
            // حفظ الحالة المحلية
            saveGameState();
        }

        // ==================== جولة السرعة ====================

        function checkSpeedRound() {
            // ✅ تحقق أفضل من أن gameState.board مصفوفة
            if (!gameState.board || typeof gameState.board !== 'object' || gameState.board.length === undefined) {
                console.error('❌ gameState.board غير صالحة:', gameState.board);
                return;
            }
            
            // ✅ تحويل إلى مصفوفة إذا لزم الأمر
            const boardArray = Array.isArray(gameState.board) ? gameState.board : Array.from(gameState.board);
            
            const remaining = boardArray.filter(cell => cell === null).length;
            console.log('🔍 فحص جولة السرعة - خلايا متبقية:', remaining, 'مفعلة:', speedRoundActive, 'مشغلة:', gameState.speedRoundTriggered);
            
            // ✅ الحل الجديد: تفعيل عند 5 خلايا أو أقل مع تتبع التغيير
            if (remaining <= 5 && previousRemainingCells > 5 && !speedRoundActive && !gameState.speedRoundTriggered) {
                console.log('🚀 شروط جولة السرعة متوفرة - جاري التفعيل!');
                gameState.speedRoundTriggered = true;
                activateSpeedRoundOnServer();
            }
            
            previousRemainingCells = remaining;
            
            // ✅ تحقق إضافي: إذا كانت مفعلة ولكن غير معروضة
            if (speedRoundActive && document.getElementById('speedRound').style.display === 'none') {
                console.log('🔄 إعادة إظهار جولة السرعة (كانت مخفية)');
                document.getElementById('speedRound').style.display = 'block';
            }
        }

        async function activateSpeedRoundOnServer() {
            showLoading('تفعيل جولة السرعة...');
            
            try {
                console.log('🔄 محاولة تفعيل جولة السرعة على السيرفر...');
                
                const response = await fetch(`/game/${gameState.gameId}/activate-speed-round`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                
                if (response.ok) {
                    const result = await response.json();
                    console.log('✅ تم تفعيل جولة السرعة على السيرفر:', result);
                    
                    // التفعيل المحلي فوراً
                    activateSpeedRoundLocally();
                } else {
                    console.warn('⚠️ السيرفر لم يستجب، التفعيل محلياً فقط');
                    activateSpeedRoundLocally();
                }
            } catch (error) {
                console.error('❌ فشل في تفعيل جولة السرعة على السيرفر:', error);
                // التفعيل المحلي كبديل
                activateSpeedRoundLocally();
            } finally {
                hideLoading();
            }
        }

        function activateSpeedRoundLocally() {
            console.log('🎯 تفعيل جولة السرعة محلياً!');
            
            speedRoundActive = true;
            gameState.speedRoundActive = true;
            gameState.speedRoundTriggered = true;
            
            // التحقق من وجود العنصر أولاً
            let speedRoundElement = document.getElementById('speedRound');
            
            if (!speedRoundElement) {
                console.log('❌ عنصر جولة السرعة غير موجود، جاري إنشائه...');
                speedRoundElement = document.createElement('div');
                speedRoundElement.id = 'speedRound';
                speedRoundElement.className = 'speed-round-indicator';
                speedRoundElement.innerHTML = `
                    <div class="speed-round-title">🎯 جولة السرعة!</div>
                    <p>أول من يجب بشكل صحيح يمكنه استبدال أي مربع للخصم!</p>
                `;
                
                // إدراج بعد لوحة اللعبة
                const gameBoard = document.querySelector('.game-board');
                if (gameBoard && gameBoard.parentNode) {
                    gameBoard.parentNode.insertBefore(speedRoundElement, gameBoard.nextSibling);
                } else {
                    // إذا فشل، ضعه في قسم اللوحة
                    const gameBoardSection = document.querySelector('.game-board-section');
                    if (gameBoardSection) {
                        gameBoardSection.appendChild(speedRoundElement);
                    }
                }
            }
            
            // إظهار العنصر
            speedRoundElement.style.display = 'block';
            console.log('✅ تم إظهار عنصر جولة السرعة');
            
            showNotification('🎯 جولة السرعة! أول من يجب بشكل صحيح يمكنه استبدال أي مربع للخصم!', 'info');
            sounds.notification();
            
            // إذا كان دور اللاعب، اعرض السؤال فوراً
            if (gameState.isPlayerTurn && gameState.gameStatus === 'active') {
                console.log('🎯 دور اللاعب، جاري جلب سؤال جولة السرعة...');
                fetchQuestion();
            }
            
            saveGameState();
        }

        // ==================== استبدال جولة السرعة ====================

        function showReplaceModal() {
            if (!speedRoundWinner) {
                showNotification('❌ لست الفائز في جولة السرعة', 'error');
                return;
            }

            const playerSymbol = gameState.currentTurn === 'player1' ? 'X' : 'O';
            const opponentSymbol = playerSymbol === 'X' ? 'O' : 'X';
            
            let boardHtml = '';
            for (let i = 0; i < 9; i++) {
                const cellValue = gameState.board[i];
                const isOpponentCell = cellValue === opponentSymbol;
                const isSelectable = isOpponentCell && speedRoundWinner && !speedRoundUsed;
                
                boardHtml += `
                    <div class="board-cell replace-cell no-select ${isSelectable ? 'selectable' : 'disabled'}" 
                        data-position="${i}" onclick="selectReplaceCell(${i})">
                        ${cellValue || ''}
                        ${isSelectable ? '<div class="replace-overlay">🔁</div>' : ''}
                    </div>
                `;
            }
            
            document.getElementById('replaceBoard').innerHTML = boardHtml;
            document.getElementById('replaceModal').style.display = 'flex';
        }

        function selectReplaceCell(position) {
            if (speedRoundUsed) return;

            const cell = document.querySelector(`.replace-cell[data-position="${position}"]`);
            if (!cell) return;

            document.querySelectorAll('.replace-cell').forEach(c => c.classList.remove('selected'));

            if (cell.classList.contains('selectable')) {
                cell.classList.add('selected');
                selectedReplacePosition = position;
                sounds.move();
            }
        }

        async function confirmReplace() {
            if (selectedReplacePosition === null) {
                showNotification('❌ يرجى اختيار مربع للاستبدال', 'error');
                return;
            }

            if (!speedRoundWinner) {
                showNotification('❌ لست الفائز في جولة السرعة', 'error');
                return;
            }

            showLoading('جاري استبدال المربع...');

            try {
                const playerSymbol = gameState.currentTurn === 'player1' ? 'X' : 'O';
                
                const response = await fetch(`/game/${gameState.gameId}/speed-round-move`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        position_to_replace: selectedReplacePosition,
                        new_symbol: playerSymbol,
                        speed_round_winner: true
                    })
                });

                if (!response.ok) throw new Error('Speed round move failed');
                
                const result = await response.json();
                
                speedRoundUsed = true;
                speedRoundActive = false;
                speedRoundWinner = false;
                
                updateGameState(result);
                
                showNotification('✅ تم استبدال المربع بنجاح!', 'success');
                sounds.powerup();
                document.getElementById('replaceModal').style.display = 'none';
                
                if (gameState.isAgainstComputer && !gameState.isPlayerTurn && gameState.gameStatus === 'active') {
                    startComputerMove();
                }
            } catch (error) {
                console.error('❌ Error in speed round move:', error);
                showNotification('حدث خطأ أثناء استبدال المربع', 'error');
            } finally {
                hideLoading();
            }
        }

        async function skipReplace() {
            speedRoundUsed = true;
            speedRoundActive = false;
            speedRoundWinner = false;
            document.getElementById('replaceModal').style.display = 'none';
            
            showNotification('⏭️ تم تخطي الاستبدال', 'info');
            
            try {
                await fetch(`/game/${gameState.gameId}/skip-replace`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
            } catch (error) {
                console.error('❌ Error skipping replace:', error);
            }
        }

        // ==================== Pusher والتحديث الفوري ====================

        function initPusher() {
            const key = '{{ env('PUSHER_APP_KEY', 'test-key') }}';
            const cluster = '{{ env('PUSHER_APP_CLUSTER', 'mt1') }}';

            // ✅ معالجة أخطاء الاتصال
            const pusher = new Pusher(key, {
                cluster,
                encrypted: true,
                forceTLS: true,
                authEndpoint: '/broadcasting/auth',
                auth: {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                }
            });

            gameChannel = pusher.subscribe('game.' + gameState.gameId);

            console.log('📡 Subscribed to:', 'game.' + gameState.gameId);

            gameChannel.bind('speedround.activated', (payload) => {
                console.log('🎯 استقبال حدث جولة السرعة من السيرفر:', payload);
                
                // التأكد من التفعيل حتى لو كان محلياً مفعل
                if (!speedRoundActive) {
                    activateSpeedRoundLocally();
                }
            });
            
            gameChannel.bind('game.updated', (payload) => {
                console.log('🔥 استقبال تحديث اللعبة:', payload);
                
                // ✅ تحديث فوري عند استقبال أي تحديث
                if (payload.game) {
                    updateGameState(payload.game);
                } else {
                    // إذا لم تأت البيانات، نطلبها
                    checkGameState();
                }
            });
            
            gameChannel.bind('game.left', () => {
                showNotification('👋 خرج الخصم', 'info');
                setTimeout(() => window.location.href = '/dashboard', 1500);
            });

            gameChannel.bind('pusher:subscription_succeeded', () => {
                console.log('✅ Pusher subscription succeeded');
                // ✅ تحديث الحالة فوراً بعد الاتصال
                checkGameState();
            });

            // ✅ معالجة أخطاء Pusher
            gameChannel.bind('pusher:subscription_error', (error) => {
                console.error('❌ Pusher subscription error:', error);
                showNotification('❌ خطأ في الاتصال، استخدام التحديث اليدوي', 'error');
            });
        }

        // ==================== الكمبيوتر والتحكم ====================

        function startComputerMove() {
            // ✅ إعادة تعيين إذا كانت هناك محاولات فاشلة كثيرة
            if (computerMoveAttempts >= MAX_COMPUTER_ATTEMPTS) {
                console.log('🔄 إعادة تعيين عداد محاولات الكمبيوتر');
                computerMoveAttempts = 0;
                computerMoveInProgress = false;
            }
            
            // ✅ التحقق المبدئي قبل البدء
            if (!shouldComputerMove()) {
                console.log('⏸️ إلغاء بدء حركة الكمبيوتر - الشروط غير متوفرة');
                resetComputerState();
                return;
            }

            // ✅ إلغاء أي وقت سابق إذا كان موجوداً
            if (computerMoveTimeout) {
                clearTimeout(computerMoveTimeout);
                computerMoveTimeout = null;
            }

            document.getElementById('computerThinking').style.display = 'block';
            computerMoveInProgress = true;
            computerMoveAttempts++;

            const thinkingTime = 1000 + Math.random() * 1000;
            console.log(`🤖 بدء حركة الكمبيوتر (المحاولة ${computerMoveAttempts})`);

            computerMoveTimeout = setTimeout(() => {
                // ✅ التحقق مرة أخرى قبل التنفيذ
                if (!shouldComputerMove()) {
                    console.log('⏸️ إلغاء حركة الكمبيوتر بعد الانتظار - الشروط تغيرت');
                    resetComputerState();
                    return;
                }

                triggerComputerMove();
            }, thinkingTime);
        }

        function resetComputerState() {
            computerMoveInProgress = false;
            if (computerMoveTimeout) {
                clearTimeout(computerMoveTimeout);
                computerMoveTimeout = null;
            }
            document.getElementById('computerThinking').style.display = 'none';
            hideLoading();
        }

        function shouldComputerMove() {
            // ✅ تحقق إذا اللعبة خلصت أولاً
            if (gameState.gameStatus !== 'active') {
                console.log('⏹️ إيقاف الكمبيوتر - اللعبة خلصت:', gameState.gameStatus);
                resetComputerState();
                return false;
            }
            
            // ✅ تحقق إذا في فائز
            if (gameState.winner) {
                console.log('⏹️ إيقاف الكمبيوتر - في فائز:', gameState.winner);
                resetComputerState();
                return false;
            }
            
            // ✅ تحقق إذا اللوحة مليانة
            const emptyCells = gameState.board.filter(cell => cell === null).length;
            if (emptyCells === 0) {
                console.log('⏹️ إيقاف الكمبيوتر - اللوحة مليانة');
                resetComputerState();
                return false;
            }
            
            // الباقي
            if (!gameState.isAgainstComputer) return false;
            if (gameState.isPlayerTurn) return false; 
            if (computerMoveInProgress) return false;
            
            return true;
        }

        // ✅ دالة محسنة لتحريك الكمبيوتر
        async function triggerComputerMove() {
            console.log('🤖 تشغيل حركة الكمبيوتر...');
            
            // ✅ تحقق إضافي للأمان
            if (!shouldComputerMove()) {
                console.log('⏸️ إلغاء حركة الكمبيوتر - الشروط تغيرت أثناء التنفيذ');
                resetComputerState();
                return;
            }

            showLoading('جاري تحريك الكمبيوتر...');

            try {
                const response = await fetch(`/game/${gameState.gameId}/computer-move`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                });

                if (!response.ok) {
                    const errorData = await response.json().catch(() => ({}));
                    console.warn('⚠️ السيرفر رفض حركة الكمبيوتر:', errorData);
                    
                    // ✅ Fallback: محاكاة حركة الكمبيوتر محلياً
                    if (response.status === 400 || response.status === 422) {
                        console.log('🔄 استخدام الحركة المحلية كبديل');
                        await simulateComputerMoveLocally();
                        return;
                    }
                    
                    throw new Error(errorData.error || `Computer move failed: ${response.status}`);
                }

                const freshGame = await response.json();
                console.log('✅ حركة الكمبيوتر نجحت:', freshGame);
                updateGameState(freshGame);

            } catch (error) {
                console.error('❌ Error in computer move:', error);
                showNotification(`❌ فشل في تحريك الكمبيوتر: ${error.message}`, 'error');
                
                // ✅ Fallback: محاكاة الحركة محلياً في حالة الخطأ
                await simulateComputerMoveLocally();
            } finally {
                // ✅ التأكد من إعادة تعيين الحالة في جميع الحالات
                resetComputerState();
            }
        }

        // ✅ محاكاة حركة الكمبيوتر محلياً
        async function simulateComputerMoveLocally() {
            try {
                console.log('🤖 محاكاة حركة الكمبيوتر محلياً');
                
                // اختيار حركة ذكية
                const position = chooseComputerMoveLocally();
                if (position === -1) {
                    console.log('❌ لا توجد حركات متاحة');
                    return;
                }
                
                // محاكاة الإجابة على السؤال (75% فرصة صحيحة)
                const isCorrect = Math.random() < 0.75;
                const selectedOption = isCorrect ? '0' : Math.floor(Math.random() * 4).toString();
                
                if (isCorrect) {
                    // تطبيق الحركة على اللوحة
                    const playerSymbol = 'O'; // الكمبيوتر دائماً O
                    gameState.board[position] = playerSymbol;
                    
                    // تغيير الدور
                    gameState.currentTurn = 'player1';
                    gameState.isPlayerTurn = true;
                    
                    // التحقق من الفوز
                    const winner = checkWinnerLocally();
                    if (winner) {
                        gameState.winner = winner;
                        gameState.gameStatus = winner === 'O' ? 'lose' : 'win';
                    } else if (gameState.board.every(cell => cell !== null)) {
                        gameState.gameStatus = 'draw';
                    }
                    
                    updateBoard();
                    updatePlayersTurn();
                    updateGameStatus();
                    
                    showNotification('🤖 الكمبيوتر قام باللعب', 'info');
                    sounds.move();
                } else {
                    // إذا أخطأ الكمبيوتر، يعود الدور للاعب
                    gameState.currentTurn = 'player1';
                    gameState.isPlayerTurn = true;
                    updatePlayersTurn();
                    showNotification('🤖 الكمبيوتر أخطأ في الإجابة', 'info');
                }
                
                saveGameState();
                
            } catch (error) {
                console.error('❌ Error in local computer move:', error);
                showNotification('❌ فشل في محاكاة حركة الكمبيوتر', 'error');
            }
        }

        // ✅ دالة لاستعادة حالة الكمبيوتر التلقائية
        function autoRecoverComputerState() {
            // إذا كانت الحالة عالقة لأكثر من 10 ثوانٍ
            if (computerMoveInProgress && computerMoveAttempts > 2) {
                console.warn('🔄 استعادة تلقائية لحالة الكمبيوتر العالقة');
                resetComputerState();
                computerMoveAttempts = 0;
                
                // إعادة التحقق من حالة اللعبة
                setTimeout(() => {
                    if (shouldComputerMove()) {
                        console.log('🔄 إعادة بدء حركة الكمبيوتر بعد الاستعادة');
                        startComputerMove();
                    }
                }, 1000);
            }
        }

        // ✅ تشغيل الاستعادة التلقائية كل 10 ثوانٍ
        setInterval(autoRecoverComputerState, 10000);

        // ✅ اختيار حركة الكمبيوتر محلياً
        function chooseComputerMoveLocally() {
            const board = gameState.board;
            const availableMoves = [];
            
            // جمع الخلايا الفارغة
            for (let i = 0; i < 9; i++) {
                if (board[i] === null) {
                    availableMoves.push(i);
                }
            }
            
            if (availableMoves.length === 0) return -1;
            
            // استراتيجية بسيطة للكمبيوتر
            // 1. حاول الفوز
            for (const move of availableMoves) {
                const testBoard = [...board];
                testBoard[move] = 'O';
                if (checkWinningMove(testBoard, 'O')) {
                    return move;
                }
            }
            
            // 2. حاول منع اللاعب من الفوز
            for (const move of availableMoves) {
                const testBoard = [...board];
                testBoard[move] = 'X';
                if (checkWinningMove(testBoard, 'X')) {
                    return move;
                }
            }
            
            // 3. المركز أولاً
            if (availableMoves.includes(4)) {
                return 4;
            }
            
            // 4. الزوايا
            const corners = [0, 2, 6, 8];
            const availableCorners = corners.filter(corner => availableMoves.includes(corner));
            if (availableCorners.length > 0) {
                return availableCorners[Math.floor(Math.random() * availableCorners.length)];
            }
            
            // 5. أي حركة عشوائية
            return availableMoves[Math.floor(Math.random() * availableMoves.length)];
        }

        // ✅ التحقق من حركة فوز محلياً
        function checkWinningMove(board, player) {
            const winningCombinations = [
                [0, 1, 2], [3, 4, 5], [6, 7, 8], // صفوف
                [0, 3, 6], [1, 4, 7], [2, 5, 8], // أعمدة
                [0, 4, 8], [2, 4, 6] // أقطار
            ];
            
            return winningCombinations.some(combination => {
                return combination.every(index => board[index] === player);
            });
        }

        // ✅ التحقق من الفائز محلياً
        function checkWinnerLocally() {
            const board = gameState.board;
            const winningCombinations = [
                [0, 1, 2], [3, 4, 5], [6, 7, 8], // صفوف
                [0, 3, 6], [1, 4, 7], [2, 5, 8], // أعمدة
                [0, 4, 8], [2, 4, 6] // أقطار
            ];
            
            for (const combo of winningCombinations) {
                const [a, b, c] = combo;
                if (board[a] && board[a] === board[b] && board[a] === board[c]) {
                    return board[a]; // 'X' أو 'O'
                }
            }
            
            return null;
        }
        // ==================== نظام التعافي من الأخطاء ====================

        async function safeApiCall(apiCall, errorMessage, showLoading = true) {
            if (showLoading) {
                showLoading('جاري المعالجة...');
            }
            
            try {
                const result = await apiCall();
                errorCount = 0; // إعادة تعيين عداد الأخطاء عند النجاح
                return result;
            } catch (error) {
                errorCount++;
                console.error(`❌ ${errorMessage}:`, error);
                
                if (errorCount >= MAX_ERRORS) {
                    showNotification('🔧 مشكلة في الاتصال، جاري إعادة التحميل...', 'error', 0);
                    setTimeout(() => window.location.reload(), 3000);
                } else {
                    showNotification(`⚠️ ${errorMessage} (محاولة ${errorCount}/${MAX_ERRORS})`, 'warning');
                }
                
                throw error;
            } finally {
                if (showLoading) {
                    hideLoading();
                }
            }
        }

        // ==================== الدوال المساعدة ====================

        async function checkGameState() {
            const now = Date.now();
            if (now - lastUpdateTime < UPDATE_COOLDOWN) {
                return; // تجنب التحديث المتكرر
            }
            
            lastUpdateTime = now;
            
            return safeApiCall(async () => {
                const response = await fetch(`/game/${gameState.gameId}/state`);
                if (!response.ok) throw new Error('Network error');
                const gameData = await response.json();
                updateGameState(gameData);
                document.getElementById('computerThinking').style.display = 'none';
                return gameData;
            }, 'فشل في التحقق من حالة اللعبة', false);
        }

        async function forceRefresh() {
            showNotification('🔄 جاري تحديث اللوحة...', 'info');
            try {
                await checkGameState();
                showNotification('✅ تم تحديث اللوحة بنجاح', 'success');
            } catch (error) {
                console.error('❌ Refresh failed:', error);
                showNotification('❌ فشل في تحديث اللوحة', 'error');
                
                // ✅ محاولة بديلة إذا فشل التحديث
                setTimeout(async () => {
                    try {
                        await checkGameState();
                    } catch (e) {
                        console.error('❌ Failed again:', e);
                    }
                }, 1000);
            }
        }

        async function restartGame() {
            const button = document.getElementById('restartBtn');
            setButtonLoading(button, true);
            
            try {
                const response = await fetch(`/game/${gameState.gameId}/restart`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                });

                if (!response.ok) throw new Error('Restart failed');
                
                const result = await response.json();
                if (result.success) {
                    clearBackup();
                    window.location.href = `/game/${result.game_id}`;
                }
            } catch (error) {
                console.error('❌ Error restarting game:', error);
                window.location.href = '/dashboard';
            } finally {
                setButtonLoading(button, false);
            }
        }

        function showNotification(message, type = 'info', duration = 3000) {
            // إزالة الإشعارات القديمة
            const existingNotifications = document.querySelectorAll('.notification');
            existingNotifications.forEach(notification => {
                notification.remove();
            });

            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            
            // إضافة زر إغلاق للإشعارات المهمة
            if (type === 'error' || duration === 0) {
                const closeBtn = document.createElement('button');
                closeBtn.className = 'notification-close';
                closeBtn.innerHTML = '✕';
                closeBtn.onclick = () => notification.remove();
                notification.appendChild(closeBtn);
            }
            
            const messageSpan = document.createElement('span');
            messageSpan.textContent = message;
            notification.appendChild(messageSpan);
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.classList.add('show');
            }, 100);
            
            if (duration > 0) {
                setTimeout(() => {
                    notification.classList.remove('show');
                    setTimeout(() => {
                        if (notification.parentNode) {
                            notification.parentNode.removeChild(notification);
                        }
                    }, 300);
                }, duration);
            }
            
            sounds.notification();
        }

        function getDifficultyText(difficulty) {
            const difficulties = {
                'easy': 'سهل',
                'medium': 'متوسط',
                'hard': 'صعب'
            };
            return difficulties[difficulty] || difficulty;
        }

        function getOptionLabel(index) {
            const labels = ['أ', 'ب', 'ج', 'د'];
            return labels[index] || (index + 1);
        }

        function resetQuestionModal() {
            gameState.selectedOption = null;
            gameState.showFeedback = false;
            gameState.currentQuestion = null;
        }

        // ==================== التحكم في اللعبة ====================

        function confirmExit() {
            document.getElementById('exitModal').style.display = 'flex';
        }

        function closeExitModal() {
            document.getElementById('exitModal').style.display = 'none';
        }

        async function proceedExit() {
            closeExitModal();
            showLoading('جاري الخروج...');

            try {
                const res = await fetch(`/game/${gameState.gameId}/forfeit`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                if (!res.ok) throw new Error('forfeit failed');
                
                clearBackup();
                window.location.href = '/dashboard';
            } catch (e) {
                console.warn('Error in forfeit:', e);
                showNotification('❌ حدث خطأ أثناء الخروج', 'error');
            } finally {
                hideLoading();
            }
        }

        function startAutoRefresh() {
            // ✅ تحديث كل 5 ثواني للتأكد من التزامن
            autoRefreshInterval = setInterval(async () => {
                if (gameState.gameStatus === 'active') {
                    await checkGameState();
                }
            }, 5000);
        }

        function stopAutoRefresh() {
            if (autoRefreshInterval) {
                clearInterval(autoRefreshInterval);
                autoRefreshInterval = null;
            }
        }

        // ==================== إدارة الذاكرة والأحداث ====================

        function cleanupEventListeners() {
            // إزالة جميع مستمعي الأحداث المضافة ديناميكياً
            const elements = document.querySelectorAll('[data-event-added]');
            elements.forEach(element => {
                const clone = element.cloneNode(true);
                element.parentNode.replaceChild(clone, element);
            });
            
            // إيقاف Pusher
            if (gameChannel) {
                gameChannel.unbind_all();
                gameChannel.unsubscribe();
            }
            
            // إيقاف المؤقتات
            stopAutoRefresh();
            if (countdownInterval) {
                clearInterval(countdownInterval);
            }
            
            // إغلاق AudioContext
            if (audioContext && audioContext.state !== 'closed') {
                audioContext.close();
            }
            resetComputerState();
            computerMoveAttempts = 0;

            // ✅ إزالة مستمعي أحداث الكمبيوتر
            const computerElements = document.querySelectorAll('.computer-thinking, .thinking-animation');
            computerElements.forEach(element => {
                const clone = element.cloneNode(true);
                element.parentNode.replaceChild(clone, element);
            });
        }

        // ==================== إدارة حالة الاتصال ====================

        function handleOnline() {
            isOnline = true;
            showNotification('✅ تم استعادة الاتصال', 'success');
            // مزامنة الحالة مع السيرفر
            checkGameState();
        }

        function handleOffline() {
            isOnline = false;
            showNotification('⚠️ فقدان الاتصال، جاري استخدام النسخة المحلية', 'warning', 0);
            // استخدام النسخة المحلية
            if (!loadGameState()) {
                showNotification('❌ لا توجد نسخة محلية، انتظر اتصال الإنترنت', 'error', 0);
            }
        }

        // ==================== التهيئة ====================

        function initGame() {
            updateBoard();
            updatePlayersTurn();
            updateGameStatus();
            checkSpeedRound();
            loadPowerUps(); // تحميل البطاقات
            
            // محاولة تحميل الحالة المحلية
            if (!isOnline) {
                loadGameState();
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            initGame();
            initPusher();
            startAutoRefresh();
            
            // إضافة مستمعي أحداث الاتصال
            window.addEventListener('online', handleOnline);
            window.addEventListener('offline', handleOffline);
            
            // إضافة مستمع لتنظيف الذاكرة عند مغادرة الصفحة
            window.addEventListener('beforeunload', cleanupEventListeners);
            window.addEventListener('pagehide', cleanupEventListeners);
            
            if (gameState.isAgainstComputer && !gameState.isPlayerTurn && gameState.gameStatus === 'active') {
                startComputerMove();
            }
        });

        // منع إغلاق الصفحة أثناء اللعبة النشطة
        window.addEventListener('beforeunload', function(e) {
            if (gameState.gameStatus === 'active') {
                e.preventDefault();
                e.returnValue = 'هل أنت متأكد من أنك تريد المغادرة؟ قد تخسر النقاط.';
                return e.returnValue;
            }
        });
    </script>
</body>
</html>