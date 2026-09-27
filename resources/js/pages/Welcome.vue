<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

const servicios = [
    {
        titulo: 'Mantenimiento preventivo',
        descripcion:
            'Cambios de aceite, revisión de frenos, cadena y demás puntos clave para mantener tu moto en óptimas condiciones.',
    },
    {
        titulo: 'Reparación general',
        descripcion:
            'Diagnóstico y reparación de motor, transmisión, sistema eléctrico y más, con repuestos de calidad.',
    },
    {
        titulo: 'Repuestos',
        descripcion:
            'Contamos con inventario propio de repuestos originales y compatibles para las principales marcas.',
    },
    {
        titulo: 'Diagnóstico especializado',
        descripcion:
            'Revisión técnica completa antes de cualquier viaje largo o compra de segunda mano.',
    },
];
</script>

<template>
    <div class="landing">
        <header class="landing-nav">
            <span class="landing-brand">Taller JEJ</span>
            <Link href="/login" class="landing-btn landing-btn-outline"
                >Iniciar sesión</Link
            >
        </header>

        <section class="landing-hero">
            <h1>Taller JEJ</h1>
            <p>
                Mantenimiento, reparación y repuestos para tu motocicleta, en un
                solo lugar.
            </p>
            <Link href="/login" class="landing-btn landing-btn-primary"
                >Iniciar sesión</Link
            >
        </section>

        <section class="landing-servicios">
            <h2>Nuestros servicios</h2>
            <div class="landing-servicios-grid">
                <div
                    v-for="servicio in servicios"
                    :key="servicio.titulo"
                    class="landing-card"
                >
                    <h3>{{ servicio.titulo }}</h3>
                    <p>{{ servicio.descripcion }}</p>
                </div>
            </div>
        </section>

        <section class="landing-nosotros">
            <h2>Sobre nosotros</h2>
            <p>
                En Taller JEJ trabajamos con compromiso y experiencia para que
                tu motocicleta esté siempre lista para rodar. Combinamos
                atención personalizada con procesos claros: sabrás en todo
                momento el estado de tu servicio y el costo asociado.
            </p>
        </section>

        <section class="landing-contacto">
            <h2>Contacto</h2>
            <ul>
                <li>
                    <strong>Dirección:</strong> Ej: Calle 10 #5-23, tu ciudad
                </li>
                <li><strong>Teléfono:</strong> Ej: 300 000 0000</li>
                <li>
                    <strong>Horario:</strong> Lunes a sábado, 8:00 a.m. - 6:00
                    p.m.
                </li>
            </ul>
        </section>

        <footer class="landing-footer">
            <p>
                Taller JEJ — Proyecto de grado, Tecnología en Desarrollo de
                Software.
            </p>
        </footer>
    </div>
</template>

<style scoped>
.landing {
    font-family: 'Instrument Sans', system-ui, sans-serif;
    color: #1f2430;
}

.landing-nav {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.25rem 2rem;
    border-bottom: 1px solid #e5e5e5;
}

.landing-brand {
    font-size: 1.25rem;
    font-weight: 700;
}

.landing-btn {
    display: inline-block;
    padding: 0.6rem 1.4rem;
    border-radius: 6px;
    font-weight: 600;
    text-decoration: none;
    transition: opacity 0.15s ease;
}

.landing-btn:hover {
    opacity: 0.85;
}

.landing-btn-primary {
    background: #d9480f;
    color: #fff;
}

.landing-btn-outline {
    border: 1px solid #d9480f;
    color: #d9480f;
}

.landing-hero {
    text-align: center;
    padding: 5rem 2rem;
    background: #1f2430;
    color: #fff;
}

.landing-hero h1 {
    font-size: 3rem;
    margin-bottom: 1rem;
}

.landing-hero p {
    font-size: 1.15rem;
    margin-bottom: 2rem;
    color: #cbd0dc;
}

.landing-servicios,
.landing-nosotros,
.landing-contacto {
    max-width: 960px;
    margin: 0 auto;
    padding: 4rem 2rem;
}

.landing-servicios h2,
.landing-nosotros h2,
.landing-contacto h2 {
    font-size: 1.75rem;
    margin-bottom: 2rem;
    text-align: center;
}

.landing-servicios-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 1.5rem;
}

.landing-card {
    padding: 1.5rem;
    border: 1px solid #e5e5e5;
    border-radius: 10px;
}

.landing-card h3 {
    margin-bottom: 0.5rem;
    color: #d9480f;
}

.landing-nosotros p {
    line-height: 1.7;
    font-size: 1.05rem;
    text-align: center;
}

.landing-contacto ul {
    list-style: none;
    padding: 0;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    max-width: 480px;
    margin: 0 auto;
}

.landing-footer {
    text-align: center;
    padding: 2rem;
    border-top: 1px solid #e5e5e5;
    color: #6b7280;
    font-size: 0.9rem;
}
</style>
