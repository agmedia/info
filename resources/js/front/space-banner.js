const clamp = (value, min, max) => Math.min(max, Math.max(min, value));

const createStars = (count, width, height) => {
    const stars = [];

    for (let index = 0; index < count; index += 1) {
        stars.push({
            x: Math.random() * width,
            y: Math.random() * height,
            radius: 0.5 + Math.random() * 1.6,
            alpha: 0.2 + Math.random() * 0.55,
            twinkleRate: 0.8 + Math.random() * 2,
            phase: Math.random() * Math.PI * 2,
            driftX: (Math.random() - 0.5) * 0.8,
            driftY: (Math.random() - 0.5) * 0.4,
        });
    }

    return stars;
};

const createSatellites = (earth) => ([
    {
        angle: Math.PI * 0.16,
        speed: 0.48,
        orbitX: earth.radius * 1.6,
        orbitY: earth.radius * 0.9,
        trail: [],
        color: 'rgba(255, 141, 221, 0.95)',
    },
    {
        angle: Math.PI * 0.78,
        speed: 0.36,
        orbitX: earth.radius * 2.05,
        orbitY: earth.radius * 1.2,
        trail: [],
        color: 'rgba(133, 231, 255, 0.92)',
    },
    {
        angle: Math.PI * 1.38,
        speed: 0.58,
        orbitX: earth.radius * 1.38,
        orbitY: earth.radius * 0.74,
        trail: [],
        color: 'rgba(255, 201, 126, 0.94)',
    },
    {
        angle: Math.PI * 1.92,
        speed: 0.41,
        orbitX: earth.radius * 2.35,
        orbitY: earth.radius * 1.45,
        trail: [],
        color: 'rgba(168, 198, 255, 0.9)',
    },
]);

const drawStarField = (ctx, stars, time, width, height) => {
    ctx.save();

    stars.forEach((star) => {
        star.x += star.driftX * 0.01;
        star.y += star.driftY * 0.01;

        if (star.x < -2) star.x = width + 2;
        if (star.x > width + 2) star.x = -2;
        if (star.y < -2) star.y = height + 2;
        if (star.y > height + 2) star.y = -2;

        const flicker = 0.3 + 0.7 * (0.5 + Math.sin(time * star.twinkleRate + star.phase) * 0.5);
        const alpha = star.alpha * flicker;

        ctx.beginPath();
        ctx.fillStyle = `rgba(236, 247, 255, ${alpha.toFixed(3)})`;
        ctx.arc(star.x, star.y, star.radius, 0, Math.PI * 2);
        ctx.fill();
    });

    ctx.restore();
};

const drawNebula = (ctx, width, height) => {
    const leftGlow = ctx.createRadialGradient(width * 0.18, height * 0.2, 20, width * 0.18, height * 0.2, width * 0.5);
    leftGlow.addColorStop(0, 'rgba(224, 83, 191, 0.24)');
    leftGlow.addColorStop(1, 'rgba(224, 83, 191, 0)');

    const rightGlow = ctx.createRadialGradient(width * 0.78, height * 0.56, 24, width * 0.78, height * 0.56, width * 0.6);
    rightGlow.addColorStop(0, 'rgba(52, 151, 255, 0.18)');
    rightGlow.addColorStop(1, 'rgba(52, 151, 255, 0)');

    ctx.fillStyle = leftGlow;
    ctx.fillRect(0, 0, width, height);

    ctx.fillStyle = rightGlow;
    ctx.fillRect(0, 0, width, height);
};

const drawEarth = (ctx, earth, time) => {
    const { x, y, radius } = earth;

    const halo = ctx.createRadialGradient(x, y, radius * 0.72, x, y, radius * 2.2);
    halo.addColorStop(0, 'rgba(87, 169, 255, 0.52)');
    halo.addColorStop(1, 'rgba(87, 169, 255, 0)');
    ctx.fillStyle = halo;
    ctx.beginPath();
    ctx.arc(x, y, radius * 2.2, 0, Math.PI * 2);
    ctx.fill();

    ctx.save();
    ctx.beginPath();
    ctx.arc(x, y, radius, 0, Math.PI * 2);
    ctx.clip();

    const ocean = ctx.createRadialGradient(x - radius * 0.28, y - radius * 0.42, radius * 0.2, x, y, radius * 1.08);
    ocean.addColorStop(0, '#7fd5ff');
    ocean.addColorStop(0.52, '#2369b9');
    ocean.addColorStop(1, '#0b2148');
    ctx.fillStyle = ocean;
    ctx.fillRect(x - radius, y - radius, radius * 2, radius * 2);

    ctx.fillStyle = 'rgba(108, 210, 129, 0.55)';
    const drift = Math.sin(time * 0.22) * radius * 0.08;

    ctx.beginPath();
    ctx.ellipse(x - radius * 0.22 + drift, y - radius * 0.12, radius * 0.4, radius * 0.24, -0.4, 0, Math.PI * 2);
    ctx.ellipse(x + radius * 0.24 - drift * 0.6, y + radius * 0.16, radius * 0.3, radius * 0.18, 0.45, 0, Math.PI * 2);
    ctx.ellipse(x + radius * 0.02 + drift * 0.3, y - radius * 0.36, radius * 0.24, radius * 0.13, 0.2, 0, Math.PI * 2);
    ctx.fill();

    ctx.strokeStyle = 'rgba(255, 255, 255, 0.2)';
    ctx.lineWidth = 1;
    for (let band = 0; band < 4; band += 1) {
        const offset = -radius * 0.5 + band * radius * 0.33 + Math.sin(time * 0.6 + band) * 2;
        ctx.beginPath();
        ctx.ellipse(x, y + offset, radius * 0.95, radius * 0.16, 0, 0, Math.PI * 2);
        ctx.stroke();
    }

    const darkSide = ctx.createLinearGradient(x - radius, y, x + radius * 0.9, y);
    darkSide.addColorStop(0, 'rgba(4, 10, 19, 0.76)');
    darkSide.addColorStop(0.55, 'rgba(4, 10, 19, 0.2)');
    darkSide.addColorStop(1, 'rgba(4, 10, 19, 0)');
    ctx.fillStyle = darkSide;
    ctx.fillRect(x - radius, y - radius, radius * 2, radius * 2);

    ctx.restore();

    ctx.lineWidth = 1.7;
    ctx.strokeStyle = 'rgba(166, 220, 255, 0.62)';
    ctx.beginPath();
    ctx.arc(x, y, radius, 0, Math.PI * 2);
    ctx.stroke();
};

const drawOrbits = (ctx, earth, time) => {
    ctx.save();
    ctx.strokeStyle = 'rgba(181, 224, 255, 0.2)';
    ctx.lineWidth = 1;
    ctx.setLineDash([7, 8]);

    for (let ring = 0; ring < 3; ring += 1) {
        const wobble = Math.sin(time * 0.22 + ring * 1.7) * 0.05;
        const radiusX = earth.radius * (1.4 + ring * 0.35);
        const radiusY = earth.radius * (0.72 + ring * 0.22 + wobble);

        ctx.beginPath();
        ctx.ellipse(earth.x, earth.y, radiusX, radiusY, 0.08 * (ring + 1), 0, Math.PI * 2);
        ctx.stroke();
    }

    ctx.restore();
};

const drawSatellite = (ctx, satellite, earth) => {
    const x = earth.x + Math.cos(satellite.angle) * satellite.orbitX;
    const y = earth.y + Math.sin(satellite.angle) * satellite.orbitY;

    satellite.trail.push({ x, y });
    if (satellite.trail.length > 24) {
        satellite.trail.shift();
    }

    if (satellite.trail.length > 1) {
        for (let index = 1; index < satellite.trail.length; index += 1) {
            const previous = satellite.trail[index - 1];
            const current = satellite.trail[index];
            const fade = index / satellite.trail.length;

            ctx.strokeStyle = `rgba(170, 215, 255, ${(fade * 0.34).toFixed(3)})`;
            ctx.lineWidth = 1.2;
            ctx.beginPath();
            ctx.moveTo(previous.x, previous.y);
            ctx.lineTo(current.x, current.y);
            ctx.stroke();
        }
    }

    ctx.save();
    ctx.translate(x, y);
    ctx.rotate(satellite.angle + Math.PI / 2);

    ctx.fillStyle = satellite.color;
    ctx.fillRect(-2.8, -6.4, 5.6, 12.8);

    ctx.fillStyle = 'rgba(108, 181, 255, 0.85)';
    ctx.fillRect(-11.4, -3.2, 7.8, 6.4);
    ctx.fillRect(3.6, -3.2, 7.8, 6.4);

    ctx.strokeStyle = 'rgba(248, 252, 255, 0.72)';
    ctx.lineWidth = 1;
    ctx.beginPath();
    ctx.moveTo(0, -6.4);
    ctx.lineTo(0, -13.8);
    ctx.stroke();

    ctx.fillStyle = 'rgba(255, 244, 188, 0.95)';
    ctx.beginPath();
    ctx.arc(0, -15.2, 1.2, 0, Math.PI * 2);
    ctx.fill();

    ctx.restore();
};

const drawShooters = (ctx, shooters, delta, width, height) => {
    for (let index = shooters.length - 1; index >= 0; index -= 1) {
        const shooter = shooters[index];
        shooter.age += delta;
        shooter.x += shooter.velocityX * delta;
        shooter.y += shooter.velocityY * delta;

        if (shooter.age >= shooter.maxAge || shooter.x > width + 80 || shooter.y > height + 80) {
            shooters.splice(index, 1);
            continue;
        }

        const life = 1 - shooter.age / shooter.maxAge;
        const tailX = shooter.x - shooter.velocityX * 0.08;
        const tailY = shooter.y - shooter.velocityY * 0.08;

        ctx.strokeStyle = `rgba(180, 226, 255, ${(life * 0.75).toFixed(3)})`;
        ctx.lineWidth = 1.3;
        ctx.beginPath();
        ctx.moveTo(shooter.x, shooter.y);
        ctx.lineTo(tailX, tailY);
        ctx.stroke();
    }
};

const mountBanner = (banner) => {
    if (!(banner instanceof HTMLElement) || banner.dataset.spaceReady === '1') {
        return;
    }

    const canvas = banner.querySelector('[data-space-canvas]');
    if (!(canvas instanceof HTMLCanvasElement)) {
        return;
    }

    const context = canvas.getContext('2d');
    if (!context) {
        return;
    }

    banner.dataset.spaceReady = '1';

    const reducedMotionMedia = window.matchMedia('(prefers-reduced-motion: reduce)');

    const state = {
        width: 0,
        height: 0,
        earth: { x: 0, y: 0, radius: 0 },
        stars: [],
        satellites: [],
        shooters: [],
        running: false,
        rafId: 0,
        lastStamp: 0,
        sceneTime: 0,
    };

    const recalculateScene = () => {
        const width = Math.max(1, Math.round(banner.clientWidth));
        const height = Math.max(1, Math.round(banner.clientHeight));
        const dpr = clamp(window.devicePixelRatio || 1, 1, 2);

        state.width = width;
        state.height = height;

        canvas.width = Math.round(width * dpr);
        canvas.height = Math.round(height * dpr);
        canvas.style.width = `${width}px`;
        canvas.style.height = `${height}px`;

        context.setTransform(dpr, 0, 0, dpr, 0, 0);

        const earthRadius = clamp(Math.min(height * 0.26, width * 0.15), 56, 130);
        state.earth = {
            x: width * 0.78,
            y: height * 0.52,
            radius: earthRadius,
        };

        const starCount = clamp(Math.round((width * height) / 7000), 85, 220);
        state.stars = createStars(starCount, width, height);
        state.satellites = createSatellites(state.earth);
        state.shooters = [];
    };

    const drawFrame = (delta) => {
        const { width, height, stars, earth, satellites, shooters } = state;

        context.clearRect(0, 0, width, height);

        drawNebula(context, width, height);
        drawStarField(context, stars, state.sceneTime, width, height);
        drawOrbits(context, earth, state.sceneTime);
        drawEarth(context, earth, state.sceneTime);

        satellites.forEach((satellite) => {
            if (!reducedMotionMedia.matches) {
                satellite.angle += satellite.speed * delta;
            }
            drawSatellite(context, satellite, earth);
        });

        if (!reducedMotionMedia.matches && Math.random() < 0.012) {
            shooters.push({
                x: Math.random() * width * 0.55,
                y: Math.random() * height * 0.24,
                velocityX: 340 + Math.random() * 200,
                velocityY: 120 + Math.random() * 120,
                age: 0,
                maxAge: 0.95 + Math.random() * 0.5,
            });
        }

        drawShooters(context, shooters, delta, width, height);
    };

    const stop = () => {
        state.running = false;
        if (state.rafId) {
            window.cancelAnimationFrame(state.rafId);
            state.rafId = 0;
        }
    };

    const tick = (stamp) => {
        if (!state.running) {
            return;
        }

        if (!banner.isConnected) {
            stop();
            return;
        }

        const elapsed = state.lastStamp ? (stamp - state.lastStamp) / 1000 : 0.016;
        const delta = Math.min(0.045, Math.max(0.001, elapsed));
        state.lastStamp = stamp;
        state.sceneTime += delta;

        drawFrame(delta);

        state.rafId = window.requestAnimationFrame(tick);
    };

    const start = () => {
        if (state.running || reducedMotionMedia.matches) {
            return;
        }

        state.running = true;
        state.lastStamp = 0;
        state.rafId = window.requestAnimationFrame(tick);
    };

    recalculateScene();

    if (reducedMotionMedia.matches) {
        drawFrame(0);
    } else {
        start();
    }

    let resizeTimer = null;
    const onResize = () => {
        if (resizeTimer) {
            window.clearTimeout(resizeTimer);
        }

        resizeTimer = window.setTimeout(() => {
            recalculateScene();
            if (reducedMotionMedia.matches) {
                drawFrame(0);
            }
        }, 90);
    };

    const onVisibilityChange = () => {
        if (document.hidden) {
            stop();
            return;
        }

        if (reducedMotionMedia.matches) {
            drawFrame(0);
            return;
        }

        start();
    };

    const onMotionChange = () => {
        if (reducedMotionMedia.matches) {
            stop();
            drawFrame(0);
            return;
        }

        start();
    };

    window.addEventListener('resize', onResize);
    document.addEventListener('visibilitychange', onVisibilityChange);

    if (typeof reducedMotionMedia.addEventListener === 'function') {
        reducedMotionMedia.addEventListener('change', onMotionChange);
    } else if (typeof reducedMotionMedia.addListener === 'function') {
        reducedMotionMedia.addListener(onMotionChange);
    }
};

export const initSpaceBanner = () => {
    if (typeof document === 'undefined') {
        return;
    }

    document.querySelectorAll('[data-space-banner]').forEach(mountBanner);
};
