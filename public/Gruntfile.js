module.exports = function(grunt) {
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        ngAnnotate: {
          options: {
            singleQuotes: false
          },
          app: {
            files: [{
                expand: true,
                src: ['./js/controllers/*.js'],
                dest: './js/annotated',
                ext: '.annotated.js',
                extDot: 'last'
            }],
          }
        },
        concat: {
          js: { //target
            src: ['./js/annotated/js/controllers/*.js'],
            dest: './js/min/app.js'
          }
        },
        uglify: {
          options: {
            mangle: false
          },
          js: { //target
            src: ['./js/min/app.js'],
            dest: './js/min/app.js'
          }
        }
    });

    //load grunt tasks
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-ng-annotate');

    //register grunt default task
    grunt.registerTask('default', ['ngAnnotate', 'concat', 'uglify']);
}
