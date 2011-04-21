jQuery(document).ready(function() {
	var Ajaxlogin = {
		checkStatus: function() {
			$.ajax({
				url: tx_ajaxlogin.baseUrl,
				cache: false,
				data: {
					'tx_ajaxlogin[action]': 'checkStatus',
					'tx_ajaxlogin[controller]': 'User'
				},
				success: function(response) {
					$(tx_ajaxlogin.statusLabel).html(response.Ajaxlogin.statuslabel);
					if(response.Ajaxlogin.status == true) {
						Ajaxlogin.showUserInfo();
					} else {
						Ajaxlogin.showLoginForm();
					}
				}
			});
		},
		showLoginForm: function() {
			$.ajax({
				url: tx_ajaxlogin.baseUrl,
				cache: false,
				data: {
					'tx_ajaxlogin[action]': 'show',
					'tx_ajaxlogin[controller]': 'LoginForm'
				},
				success: function(response) {
					$(tx_ajaxlogin.placeholder).html(response.Ajaxlogin.html)
					
					$('#' + response.Ajaxlogin.formid).submit(function(event) {
						event.preventDefault();
						Ajaxlogin.validateLoginForm(response.Ajaxlogin);
					});
					
					$('#' + response.Ajaxlogin.forgotid).click(function(event) {
						event.preventDefault();
						Ajaxlogin.showForgotPasswordForm();
					});
					
					$('#' + response.Ajaxlogin.signupid).click(function(event) {
						event.preventDefault();
						Ajaxlogin.showSignupForm();
					});
				}
			});
		},
		validateLoginForm: function(formData) {
			var input = {};
			
			$('#' + formData.formid).find('input').each(function() {
				input[$(this).attr('name')] = $(this).val();
			});
			
			if(formData.RSA) {
				var rsaKey = new RSAKey();
				rsaKey.setPublic(formData.RSAKey.n, formData.RSAKey.e);
				
				var res = rsaKey.encrypt(input.pass);
				
				input.pass = 'rsa:' + hex2b64(res);
			}
			
			$.ajax({
				url: tx_ajaxlogin.baseUrl,
				cache: false,
				type: 'POST',
				data: $.extend({
					'tx_ajaxlogin[action]': 'login',
					'tx_ajaxlogin[controller]': 'User',
					'logintype': 'login',
					'pid': tx_ajaxlogin.storagePid
				}, input),
				success: function(response) {
					$(tx_ajaxlogin.statusLabel).html(response.Ajaxlogin.statuslabel);
					if(response.Ajaxlogin.status == true) {
						Ajaxlogin.showUserInfo();
					} else {
						$('#tx-ajaxlogin-notice').html(response.Ajaxlogin.message);
					}
				}
			});
		},
		showSignupForm: function() {
			$.ajax({
				url: tx_ajaxlogin.baseUrl,
				cache: false,
				data: {
					'tx_ajaxlogin[action]': 'new',
					'tx_ajaxlogin[controller]': 'User'
				},
				success: function(response) {
					$(tx_ajaxlogin.placeholder).html(response.Ajaxlogin.html);
					
					$(response.Ajaxlogin.formid).submit(function(event) {
						event.preventDefault();
					});
				}
			});
		},
		showForgotPasswordForm: function() {
			$.ajax({
				url: tx_ajaxlogin.baseUrl,
				cache: false,
				data: {
					'tx_ajaxlogin[action]': 'forgot',
					'tx_ajaxlogin[controller]': 'Password'
				},
				success: function(response) {
					$(tx_ajaxlogin.placeholder).html(response.Ajaxlogin.html);
					
					$('#' + response.Ajaxlogin.formid).submit(function(event) {
						event.preventDefault();
						
						var input = {};
						
						$('#' + response.Ajaxlogin.formid).find('input').each(function() {
							input[$(this).attr('name')] = $(this).val();
						});
						
						$.ajax({
							url: tx_ajaxlogin.baseUrl,
							cache: false,
							data: $.extend({
								'tx_ajaxlogin[action]': 'reset',
								'tx_ajaxlogin[controller]': 'Password'
							}, input),
							success: function(response) {
								$('#tx-ajaxlogin-notice').html(response.Ajaxlogin.message);
							}
						});
					});
					
					$('#' + response.Ajaxlogin.returnid).click(function(event) {
						event.preventDefault();
						Ajaxlogin.showLoginForm();
					});
				}
			});
		},
		showUserInfo: function() {
			$.ajax({
				url: tx_ajaxlogin.baseUrl,
				cache: false,
				data: {
					'tx_ajaxlogin[action]': 'show',
					'tx_ajaxlogin[controller]': 'User'
				},
				success: function(response) {
					$(tx_ajaxlogin.placeholder).html(response.Ajaxlogin.html);
					$('#' + response.Ajaxlogin.formid).submit(function(event) {
						event.preventDefault();
						$.ajax({
							url: tx_ajaxlogin.baseUrl,
							cache: false,
							data: {
								'tx_ajaxlogin[action]': 'logout',
								'tx_ajaxlogin[controller]': 'User'
							},
							success: function(response) {
								$(tx_ajaxlogin.statusLabel).html(response.Ajaxlogin.statuslabel);
								Ajaxlogin.showLoginForm();
							}
						});
					});
				}
			});
		}
	};
	
	Ajaxlogin.checkStatus();
	
	// find out if there is a "change password" form and observe the submit event
	$('form[id^=tx-ajaxlogin-password-change]').submit(function(event) {
		event.preventDefault();
		
		$('#tx-ajaxlogin-change-notice').html('');
		
		var input = {};
		
		$(this).find('input').each(function() {
			input[$(this).attr('name')] = $(this).val();
		});
		
		if(input['tx_ajaxlogin[np1]'] != input['tx_ajaxlogin[np2]']) {
			$('#tx-ajaxlogin-change-notice').html(tx_ajaxlogin.passwordNotequalMessage);
			return;
		}
		
		if(input['tx_ajaxlogin[np1]'].length < tx_ajaxlogin.minimumPasswordLength) {
			$('#tx-ajaxlogin-change-notice').html(tx_ajaxlogin.passwordTooshortMessage);
			return;
		}
		
		$.ajax({
			url: tx_ajaxlogin.baseUrl,
			cache: false,
			type: 'POST',
			data: $.extend({
				'tx_ajaxlogin[action]': 'save',
				'tx_ajaxlogin[controller]': 'Password'
			}, input),
			success: function(response) {
				$('#tx-ajaxlogin-change-notice').html(response.Ajaxlogin.message);
			}
		});
	});
});