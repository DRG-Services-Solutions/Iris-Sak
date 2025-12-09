import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        
    ],
        server: {
        host: '0.0.0.0', // Escucha en todas las interfaces de red
        cors: true,
        hmr: {
            host: '10.20.1.157' // Reemplaza con la IP de tu PC
        }
    }

});
