function App( config ){
	var self = this;
	
	self.iconsMapperDefault = 'svg-file';
	self.iconsMapper = {
		'svg-code': ['apk','bash','bat','bin','cgi','cmd','com','cpp','css','deb','exe','jar','js','jse','msi','msu','php','pkg','ps','py','rb','sh','sis','sql','vb','vbe','vbs','wsf'],
		'svg-document': ['doc','docm','docx','dot','dotm','dotx','epub','fb2','gpx','ibooks','mobi','ods','odt','oxps','pdf','pot','potm','potx','pps','ppsm','ppsx','ppt','pptm','pptx','pub','rtf','snb','txt','xls','xlsb','xlsm','xlsx','xlt','xltm','xltx','xps','djvu'],
		'svg-music': ['aac','ac3','aif','aiff','amr','aud','cdr','flac','m4a','mid','midi','mod','mp3','mpa','msc','ogg','ra','wav','wave','wma'],
		'svg-photo': ['bmp','gif','ico','jpeg','jpg','png','psd','svg','tga','tif','tiff','vst','xcf'],
		'svg-video': ['3gp','3gpp','asf','avi','f4v','flv','h264','m4v','mkv','mov','mp4','mpeg','mpg','mts','rm','ts','vcd','vid','vob','webm','wmv'],
		'svg-zip': ['7z','arj','cab','gz','gzip','pak','rar','tar','tgz','zip','zipx'],
	};
	
    Vue.mixin({
        data: function(){
            return {
                messages: config.messages,
            };
        },
        filters: {
			anchor: function( source, href ){
				return source ? source.replace('[[', '<a href="'+href+'">').replace(']]', '</a>') : source;
			},
			formatBytes: function( source, precision ){
				var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
				var value = parseInt(source);
				if( !source || value == 0 ) return '0 '+sizes[0];
				var step = 1024;
				var precision = precision ? precision : 0;
				var i = Math.min(Math.floor(Math.log(value) / Math.log(step)), sizes.length-1);
				return parseFloat((value / Math.pow(step, i)).toFixed(precision)) + ' ' + sizes[i];
			},
			beautyTrim: function( source, maxLength ){
				if( !source ) return source;
				if( maxLength < 1 ) return source;
				if( source.length <= maxLength ) return source;
				if( maxLength == 1 ) return source.substring(0,1) + '...';
				var midPoint = Math.ceil(source.length / 2);
				var removeArea = source.length - maxLength;
				var lstrip = Math.ceil(removeArea / 2);
				var rstrip = removeArea - lstrip;
				return source.substring(0, midPoint-lstrip) + '...' + source.substring(midPoint+rstrip);
			},
		},
    });
	
	Vue.component('icon', {
        template: '#icon-template',
        props: ['name'],
		computed: {
			href: function(){
				return '#'+this.name;
			}
		}
    });
	
	Vue.component('card', {
        template: '#card-template',
        data: function(){
            return {
				isShow: false,
				isIdle: false,
				isDone: false,
				isAutoscroll: false,
				isAborted: false,
				name: '',
				size: '',
                progress: 0,
				url: '',
				error: '',
            };
        },
		computed: {
			icon: function(){
				if( this.name ){
					var splited = this.name.split('.');
					var extension = splited.length > 1 ? splited.pop() : '';
					if( extension ){
						for( iconName in self.iconsMapper ){
							if( $.inArray(extension, self.iconsMapper[iconName]) >= 0 ) return iconName;
						}
					}
				}
				return self.iconsMapperDefault;
			},
			progressBarWidth: function(){
				return this.progress+'%';
			},
		},
		watch: {
			isShow: function( value ){
				if( value && this.isAutoscroll ){
					this.$nextTick(function(){
						var offset = $(this.$el).offset();
						$('html, body').animate({ scrollTop: offset.top }, 'slow');
					});
				}
			},
		},
        methods: {
			init: function( filename, totalSize, autoscroll ){
				this.name = filename;
				this.size = totalSize;
				this.isAutoscroll = !!autoscroll;
				this.isIdle = true;
				this.isShow = true;
			},
			abort: function(){
				if( this.isDone ){
					this.isShow = false;
				} else {
					if( this.isIdle ) this.setError(this.messages.canceled);
					this.isAborted = true;
				}
			},
			setProgress: function( percent ){
				this.isIdle = false;
				this.progress = Math.floor(Math.min( percent, 100 ));
			},
			setSuccess: function( url ){
				this.url = url;
				this.isDone = true;
			},
			setError: function( description ){
				this.error = description;
				this.isDone = true;
			},
			getAbortStatus: function(){
				return this.isAborted;
			},
			urlToClipboard: function(){
				if( !('clipboard' in navigator) ) return;
				navigator.clipboard.writeText(window.location.href + this.url);
			},
        },
    });
	
    self.v_main = new Vue({
        el: '#main',
		data: {
			uploads: [],
			maxFileSize: config.limits.fileSize,
			isDragging: false,
		},
		computed: {
			year: function(){
				return new Date().getFullYear();
			},
		},
		methods: {
			startDrag: function(e){
				this.isDragging = true;
			},
			stopDrag: function(e){
				this.isDragging = false;
			},
			drop: function(e){
				this.stopDrag();
				this.processFiles(e.dataTransfer.files);
			},
			openFileDialog: function(){
				var context = this;
				$('<input>')
					.attr('type','file')
					.attr('multiple','multiple')
					.change(function(){
						context.processFiles(this.files);
					})
					.click();
			},
			processFiles: function( files ){
				var context = this;
				var firstId = null;
				var canProcessNow = this.canClose();
				$.each(files, function(index, file){
					var newLength = context.uploads.push({
						status: false,
						file: file,
						card: null,
					});
					if( firstId === null ) firstId = newLength - 1;
				});
				this.$nextTick(function(){
					$.each(context.uploads, function(id, item){
						if( id < firstId ) return;
						var card = context.$refs.cards[id];
						var file = item.file;
						context.uploads[id].card = card;
						card.init(file.name, file.size, (firstId === id));
						if( file.size > context.maxFileSize ){
							card.setError(context.messages.oversize);
							context.uploads[id].status = true;
						}
					});
					if( canProcessNow ) context.processQueue();
				});
			},
			processQueue: function(){
				var context = this;
				$.each(context.uploads, function(index, item){
					if( item.status == false ){
						var card = item.card;
						if( card.getAbortStatus() ){
							context.uploads[index].status = true;
							return true;
						}
						context.uploadFileAjax(
							item.file,
							function( progress ){
								card.setProgress(progress);
								if( card.getAbortStatus() ) this.abort();
							},
							function( code, response ){
								if( code == 200 ){
									if( 'error' in response ){
										card.setError(response.error);
									} else {
										card.setSuccess(response.url);
									}
								} else {
									if( code ){
										card.setError(context.messages.httperror+code);
									} else {
										card.setError(context.messages.canceled);
									}
								}
								context.uploads[index].status = true;
								context.processQueue();
							}
						);
						
						return false;
					}
				});
			},			
			uploadFileAjax: function(file, progressCallback, responseCallback){
				var context = this;
				
				var ajaxData = new FormData();
				ajaxData.append( 'file', file );
				
				$.ajax({
					url: 'upload.php',
					type: 'post',
					data: ajaxData,
					processData: false,
					contentType: false,
					dataType: "json",
					xhr: function(){
						var xhr = new window.XMLHttpRequest();
						
						xhr.upload.addEventListener('progress', function(e){
							if( e.lengthComputable ){
								progressCallback.call(xhr, e.loaded / e.total * 100);
							}
						}, false);
						
						xhr.addEventListener('loadend', function(){
							var hasResponse = (this.status == 200) && this.response;
							responseCallback.call(xhr, this.status, hasResponse ? $.parseJSON(this.response) : {});
						}, false);

						return xhr;
					},
				});
			},
			canClose: function(){
				var result = true;
				$.each(this.uploads, function(index, item){
					result = result && item.status;
				});
				return result;
			},
		},
		mounted: function(){
			var context = this;
			
			$(this.$el).removeClass('preloader');
			
			$(window).on("beforeunload", function(){ 
				return context.canClose() ? undefined : context.messages.confirm;
			});
		},
    });
	
	return self;
}