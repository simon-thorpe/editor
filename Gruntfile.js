/*global module:false*/
module.exports = function(grunt) {
  
  var pkg=grunt.file.readJSON('package.json');

  // Project configuration.
  grunt.initConfig({
    // Metadata.
    pkg: pkg,
    trimPhpTags: function(s){s=s.substring(6);return s.substring(0,s.length-3);},
    // Task configuration.
    clean: {
      pre: ['build/','dist/','src/editor.config.php'],
      post: ['build/']
    },
    prettify: {
      options: {
        indent: 1,
        indent_char: '	',
        unformatted: [
          "noscript"
        ]
      },
      all: {
        expand: true,
        cwd: 'src/',
        src: ['*.html', 'set-password.php'],
        dest: 'src/'
      }
    },
    jscs: {
      src: 'src/**/*.js',
      options: {
        "preset": "google",
        "maximumLineLength": 1000
      }
    },
    esformatter: {
      src: 'src/**/*.js'
    },
    concat: {
      options: {
        stripBanners: true
      },
      dist: {
        src: ['src/<%= pkg.name %>.js'],
        dest: 'build/<%= pkg.name %>.min.js'
      }
    },
    uglify: {
      options: {
      },
      dist: {
        src: '<%= concat.dist.dest %>',
        dest: 'build/<%= pkg.name %>.min.js'
      }
    },
    jshint: {
      options: {
        curly: true,
        eqeqeq: true,
        immed: true,
        latedef: true,
        newcap: true,
        noarg: true,
        sub: true,
        undef: true,
        unused: true,
        boss: true,
        eqnull: true,
        browser: true,
        devel: true, // supress alert() warnings
        globals: {
          jQuery: true, // supress jQuery undefined warnings
          ace: true
        }
      },
      gruntfile: {
        src: 'Gruntfile.js'
      },
      all: {
        src: ['src/**/*.js']
      }
    },
    cssmin: {
      minify: {
        expand: true,
        cwd: 'src/',
        src: ['*.css', '!*.min.css'],
        dest: 'build/',
        ext: '.min.css'
      }
    },
    replace: {
      dist: {
        options: {
          patterns: [
            // Use functions to return value for replacement instead of "replacement: '<%= grunt.file.read("src/shell-motd.txt") %>'" otherwise regex will clash with content inside the include target.
            {
              match: 'version',
              replacement: '<%= pkg.version %>'
            },
            {
              match: 'buildDate',
              replacement: new Date().toISOString()
            },
            {
              match: /require\('commands.php'\);/,
              replacement: function(){
                var s=grunt.file.read("src/commands.php");
                s=s.substring(6); // trim leading "<?php"
                return s.substring(0,s.length-3); // trim trailing "?>"
              }
            },
            {
              match: /require\('set-password.php'\);/,
              replacement: function(){return '?>'+grunt.file.read("src/set-password.php")+'<?php';}
            },
            {
              match: /require\('login.html'\);/,
              replacement: function(){return '?>'+grunt.file.read("src/login.html")+'<?php';}
            },
            {
              match: /<\?php require\('shell-motd.txt'\); \?>/,
              replacement: function(){return grunt.file.read("src/shell-motd.txt");}
            },
            {
              match: /require\('util.php'\);/,
              replacement: function(){
                var s=grunt.file.read("src/util.php");
                s=s.substring(6); // trim leading "<?php"
                return s.substring(0,s.length-3); // trim trailing "?>"
              }
            },
            
            { // replace external css ref
              match: /<link rel="stylesheet" href="editor.css">/,
              //replacement: function(){return '<style>'+grunt.file.read("build/editor.min.css")+'</style>';}
              replacement: function(){return '<link rel="stylesheet" href="?css='+pkg.version+'">';}
            },
            { // with php handler
              match: /\/\/ @@css/,
              replacement: function(){
                var r = "if(isset($_GET['css'])){";
                r += "header('Content-Type: text/css');\n";
                r += "header('Cache-Control: public, maxage=31536000');\n"; // 1 year cache
                r += "?>"+grunt.file.read("build/editor.min.css")+"<?php\n";
                r += "exit;\n";
                r += "}";
                return r;
              }
            },
            
            { // replace external js ref
              match: /<script src="editor.js"><\/script>/,
              //replacement: function(){return '<script>'+grunt.file.read("build/editor.min.js")+'</script>';}
              replacement: function(){return '<script src="?js='+pkg.version+'"></script>';}
            },
            { // with php handler
              match: /\/\/ @@js/,
              replacement: function(){
                var r = "if(isset($_GET['js'])){";
                r += "header('Content-Type: application/javascript');\n";
                r += "header('Cache-Control: public, maxage=31536000');\n"; // 1 year cache
                r += "?>"+grunt.file.read("build/editor.min.js")+"<?php\n";
                r += "exit;\n";
                r += "}";
                return r;
              }
            },
            
            { // set default password for dist
              match: /\$PASSWORD=md5\(''\);/,
              replacement: function(){return "$PASSWORD=md5('admin');";}
            },
          ]
        },
        files: [
          {expand: true, flatten: true, src: ['src/index.php'], dest: 'dist/'}
        ]
      }
    },
    shell: {
      renameDistFile: {
        command: 'mv dist/index.php dist/editor.php'
      },
      phpLint: {
        command: 'php -l dist/editor.php'
      }
    }
  });

  // These plugins provide necessary tasks.
  grunt.loadNpmTasks('grunt-contrib-clean');
  grunt.loadNpmTasks('grunt-prettify'); // html only
  grunt.loadNpmTasks('grunt-jscs');
  grunt.loadNpmTasks('grunt-esformatter');
  grunt.loadNpmTasks('grunt-contrib-concat');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-jshint');
  grunt.loadNpmTasks('grunt-contrib-cssmin');
  grunt.loadNpmTasks('grunt-replace');
  grunt.loadNpmTasks('grunt-shell');

  // Default task.
  grunt.registerTask('lint', ['prettify', 'esformatter', 'jshint', 'jscs']);
  grunt.registerTask('default', ['clean:pre', 'prettify', 'esformatter', 'jshint', 'jscs', 'concat', 'uglify', 'cssmin', 'replace', 'clean:post', 'shell']);

};
