
function submitdata(url) {

	//event.preventDefault();
	if (typeof (jQuery("input[name='espayproduct']:checked").val()) === 'undefined') {
		alert("Please Select Payment Method");

	} else {
		var pos = jQuery("input[name='espayproduct']:checked").val();

		var posLength = pos.length;
		var n = pos.indexOf(":");
		var bankCode = pos.substr(0, n);
		var productCode = pos.substr(n + 1, posLength);

		var param = jQuery('#sgopayment_form').serialize();
		var backUrlData = escape(jQuery('#back_url').val()+"&product="+productCode);


		var data = {
			key : jQuery('#sgopaymentid').val(),
			paymentId : jQuery('#cartid').val(),
			paymentAmount : jQuery('#paymentamount').val(),
			backUrl : backUrlData,
			bankCode : bankCode,
			bankProduct : productCode
		},
		sgoPlusIframe = document.getElementById("sgoplus-iframe");
		
		
		jQuery.ajax({
			type : "POST",
			url : url,
			data : param,
			success : (function() {
				//console.log('success');
				if (sgoPlusIframe !== null)
					sgoPlusIframe.src = SGOSignature.getIframeURL(data);
					SGOSignature.receiveForm()

			})

		});

	}
}
