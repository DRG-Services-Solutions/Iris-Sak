import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
               
            ],
            refresh: true,
        }),
    ],
    
    
    //  Descomenta esto para acceder de la red local
    server: {
        host: '0.0.0.0',  // Permite conexiones desde cualquier IP
        port: 5173,        
        cors: true,
        hmr: {
            host: '192.168.100.44'
        }
    }
    
});