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
					});
					
					$('#' + response.Ajaxlogin.signupid).click(function(event) {
						event.preventDefault();
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
					'tx_ajaxlogin[controller]': 'User'
				}, input),
				success: function(response) {
					if(response.Ajaxlogin.status == true) {
						Ajaxlogin.showUserInfo();
					} else {
						$('#tx-ajaxlogin-notice').html(response.Ajaxlogin.message);
					}
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
							success: function() {
								Ajaxlogin.showLoginForm();
							}
						});
					});
				}
			});
		}
	};
	
	Ajaxlogin.checkStatus();
});