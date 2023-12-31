CNVS.Notifications = function() {
	var __core = SEMICOLON.Core;

	return {
		init: function(selector) {
			if( __core.getSelector(selector, false, false).length < 1 ){
				return true;
			}

			__core.loadJS({ file: 'plugins.bootstrap.js', id: 'canvas-bootstrap-js', jsFolder: true });

			__core.isFuncTrue( function() {
				return typeof jQuery !== 'undefined' && typeof bootstrap !== "undefined";
			}).then( function(cond) {
				if( !cond ) {
					return false;
				}

				__core.initFunction({ class: 'has-plugin-notify', event: 'pluginNotifyReady' });

				selector = __core.getSelector( selector );
				if( selector.length < 1 ){
					return true;
				}

				var element = selector,
					elPosition = element.attr('data-notify-position') || 'top-right',
					elType = element.attr('data-notify-type'),
					elMsg = element.attr('data-notify-msg') || 'Please set a message!',
					elTimeout = element.attr('data-notify-timeout') || 5000,
					elClose = element.attr('data-notify-close') || 'true',
					elAutoHide = element.attr('data-notify-autohide') || 'true',
					elId = 'toast-' + Math.floor( Math.random() * 10000 ),
					elTrigger = element.attr('data-notify-trigger') || 'self',
					elTarget = element.attr('data-notify-target'),
					elCloseHtml	= '',
					elPosClass, elTypeClass, elCloseClass;

				if( jQuery(elTarget).length > 0 && elTrigger == 'self' ) {
					var toast = bootstrap.Toast.getOrCreateInstance(jQuery(elTarget).get(0));
					toast.hide();

					jQuery(elTarget).get(0).addEventListener('hidden.bs.toast', function() {
						CNVS.Notifications.init( selector );
					});
				}

				switch( elType ){

					case 'primary':
						elTypeClass = 'text-white bg-primary border-0';
						break;

					case 'warning':
						elTypeClass = 'text-dark bg-warning border-0';
						break;

					case 'error':
						elTypeClass = 'text-white bg-danger border-0';
						break;

					case 'success':
						elTypeClass = 'text-white bg-success border-0';
						break;

					case 'info':
						elTypeClass = 'bg-info text-dark border-0';
						break;

					case 'dark':
						elTypeClass = 'text-white bg-dark border-0';
						break;

					default:
						elTypeClass = '';
						break;
				}

				switch( elPosition ){

					case 'top-left':
						elPosClass = 'top-0 start-0';
						break;

					case 'top-center':
						elPosClass = 'top-0 start-50 translate-middle-x';
						break;

					case 'middle-left':
						elPosClass = 'top-50 start-0 translate-middle-y';
						break;

					case 'middle-center':
						elPosClass = 'top-50 start-50 translate-middle';
						break;

					case 'middle-right':
						elPosClass = 'top-50 end-0 translate-middle-y';
						break;

					case 'bottom-left':
						elPosClass = 'bottom-0 start-0';
						break;

					case 'bottom-center':
						elPosClass = 'bottom-0 start-50 translate-middle-x';
						break;

					case 'bottom-right':
						elPosClass = 'bottom-0 end-0';
						break;

					default:
						elPosClass = 'top-0 end-0';
						break;
				}

				if( elType == 'info' || elType == 'warning' || !elType ) {
					elCloseClass = '';
				} else {
					elCloseClass = 'btn-close-white';
				}

				if( elClose == 'true' ) {
					elCloseHtml = '<button type="button" class="btn-close '+ elCloseClass +' btn-sm me-2 mt-2 ms-auto" data-bs-dismiss="toast" aria-label="Close"></button>';
				}

				if( elAutoHide != 'true' ) {
					elAutoHide = false;
				} else {
					elAutoHide = true;
				}

				let	elTemplate = '<div class="position-fixed '+ elPosClass +' p-3" style="z-index: 999999;">'+
				'<div id="'+ elId +'" class="toast p-2 hide '+ elTypeClass +'" role="alert" aria-live="assertive" aria-atomic="true">'+
					'<div class="d-flex">'+
						'<div class="toast-body">'+
							elMsg +
						'</div>'+
						elCloseHtml +
					'</div>'+
				'</div>';
			'</div>';

				if( elTrigger == 'self' ) {
					if( !elTarget ) {
						element.attr( 'data-notify-target', '#'+elId );
						jQuery('body').append( elTemplate );
					}
				}

				var toastElList = [].slice.call(document.querySelectorAll('.toast'));
				var toastList = toastElList.map( function(toastEl) {
					return new bootstrap.Toast(toastEl);
				});

				toastList.forEach( function(toast) {
					toast.hide();
				});

				var toastElement = element.attr('data-notify-target'),
					toastInstance = jQuery(toastElement),
					elMsgOld = toastInstance.find('.toast-body');

				if( jQuery(toastElement).length > 0 ) {
					var toast = new bootstrap.Toast( toastInstance.get(0) ,{
						delay: Number(elTimeout),
						autohide: elAutoHide,
					});

					toast.show();

					if( elTrigger == 'self' ) {
						toastInstance.get(0).addEventListener('hidden.bs.toast', function() {
							toastInstance.parent().remove();
							element.get(0).removeAttribute( 'data-notify-target' );
						});
					}
				}

				return false;

			});
		}
	};
}();
