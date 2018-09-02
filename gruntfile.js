module.exports = function(grunt) {

    // 1. Вся настройка находится здесь
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),

        concat: {
            js: {
                src: [
                    'frontend/jquery/jquery.min.js',

                    'frontend/leaflet/leaflet.js',
                    'frontend/leaflet/L.Icon.FontAwesome.js',
                    'frontend/leaflet/leaflet.markercluster.js',
                    'frontend/leaflet/L.Control.Zoomslider.js',
                    'frontend/front.js'
                ],
                dest: 'public/scripts.js',
            }
        },
        cssmin: {
            target: {
                files: {
                    'public/styles.css': [
                        'frontend/leaflet/leaflet.css',

                        'frontend/leaflet/L.Icon.FontAwesome.css',
                        'frontend/leaflet/MarkerCluster.css',
                        'frontend/leaflet/MarkerCluster.Default.css',
                        'frontend/leaflet/L.Control.Zoomslider.css',

                        'frontend/front.css'
                    ]
                }
            }
        }
    });

    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-cssmin');

    // 4. Указываем, какие задачи выполняются, когда мы вводим «grunt» в терминале
    grunt.registerTask('default', ['concat', 'cssmin']);
};