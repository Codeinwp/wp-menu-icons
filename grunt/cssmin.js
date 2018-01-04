module.exports = {
	all: {
		files: [{
			expand: true,
			cwd: 'css/',
			src: [ '*.css', '!*.min.css' ],
			dest: 'css/',
			ext: '.min.css'
		}]
	}
};
