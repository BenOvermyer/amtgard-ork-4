<?php require_once('../../../../config.php'); ?>

function urldecode(url) {
  var d = decodeURIComponent((url+'').replace(/\+/g, '%20').replace(/\+/g, '%2B'));
  return d;
}

function showLabel(id, ui) {
	if (ui != null) {
		if (typeof(ui) == 'string')
			$( id ).val( ui );
		else
			$( id ).val( ui.item.label );
	}
	return false;
}

/**
 * Converts the given data structure to a JSON string.
 * Argument: arr - The data structure that must be converted to JSON
 * Example: var json_string = array2json(['e', {pluribus: 'unum'}]);
 * 			var json = array2json({"success":"Sweet","failure":false,"empty_array":[],"numbers":[1,2,3],"info":{"name":"Binny","site":"http:\/\/www.openjs.com\/"}});
 * http://www.openjs.com/scripts/data/json_encode.php
 */
function array2json(arr) {
    var parts = [];
    var is_list = (Object.prototype.toString.apply(arr) === '[object Array]');

    for(var key in arr) {
    	var value = arr[key];
        if(typeof value == "object") { //Custom handling for arrays
            if(is_list) parts.push(array2json(value)); /* :RECURSION: */
            else parts[key] = array2json(value); /* :RECURSION: */
        } else {
            var str = "";
            if(!is_list) str = '"' + key + '":';

            //Custom handling for multiple data types
            if(typeof value == "number") str += value; //Numbers
            else if(value === false) str += 'false'; //The booleans
            else if(value === true) str += 'true';
            else str += '"' + value + '"'; //All other things
            // :TODO: Is there any more datatype we should be in the lookout for? (Functions?)

            parts.push(str);
        }
    }
    var json = parts.join(",");
    
    if(is_list) return '[' + json + ']';//Return numerical JSON
    return '{' + json + '}';//Return associative JSON
}

function extension_check(val, extensions) {
	// I stole this shit from StackOverflow ... http://stackoverflow.com/questions/651700/how-to-have-jquery-restrict-file-types-on-upload
	var ext = val.val().split('.').pop().toLowerCase();
	if($.inArray(ext, extensions) == -1) {
		return false;
	}
	return true;
}

function isNumber(n) {
    return !isNaN(parseFloat(n)) && isFinite(n);
}

$(function() {
	$( '.hasDatePicker' ).datetimepicker({ dateFormat: "yy-mm-dd", showMinute: false });
	$( '.restricted-image-type' ).change(function() {
		if (!extension_check( $( this ), ['gif','png','jpg','jpeg'])) {
			$( this ).effect("shake",{ times: 5 }, 50, 
				function () { replaceWith( $( this ).val('').clone( true ) ) } );
		}
	});
	$( '.restricted-document-type' ).change(function() {
		if (!extension_check( $( this ), ['gif','png','jpg','jpeg','pdf'])) {
			$( this ).effect("shake",{ times: 5 }, 50, 
				function () { replaceWith( $( this ).val('').clone( true ) ) } );
		}
	});
	$( '.numeric-field' ).blur(function() {
		if (!isNumber($(this).val())) {
			$( this ).val('0').fadeOut('slow', function() {
				$( this ).css('background-color', '#fff0f0');
				$( this ).css('border-color', 'red');
				$( this ).fadeIn('slow', function() {
					$( this ).animate({ borderColor: '#CCC', backgroundColor: '#fff8c0' }, 'slow' );
				});
			});
		}
	});
	$( '.integer-field').blur(function() {
	    var flash = false;
	    if (isNumber($(this).val())) {
	        var ival = Number($(this).val());
	        var cval = Math.ceil(Number($(this).val()));
	        $(this).val(cval);
	        flash = cval != ival;
	    } else {
	        flash = true;
	    }
	    if (flash)
	        $( this ).fadeOut('slow', function() {
				$( this ).css('background-color', '#fff0f0');
				$( this ).css('border-color', 'red');
				$( this ).fadeIn('slow', function() {
					$( this ).animate({ borderColor: '#CCC', backgroundColor: '#fff8c0' }, 'slow' );
				});
			});
	});
	$('.name-field').change(function() {
		var words = new RegExp("^[a-zA-Z '-,_\.]*$");
		if (!words.test($(this).val())) {
			$( this ).val('').fadeOut('slow', function() {
				$( this ).css('background-color', '#fff0f0');
				$( this ).css('border-color', 'red');
				$( this ).fadeIn('slow', function() {
					$( this ).animate({ borderColor: '#CCC', backgroundColor: '#fff8c0' }, 'slow' );
				});
			});
		}
	});
	$('.alphanumeric-field').change(function() {
		var words = new RegExp("^[a-zA-Z]+[a-zA-Z0-9]*$");
		if (!words.test($(this).val())) {
			$( this ).val('').fadeOut('slow', function() {
				$( this ).css('background-color', '#fff0f0');
				$( this ).css('border-color', 'red');
				$( this ).fadeIn('slow', function() {
					$( this ).animate({ borderColor: '#CCC', backgroundColor: '#fff8c0' }, 'slow' );
				});
			});
		}
	});
	$('.most-emails-field').change(function() {
		var words = new RegExp("^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+$");
		if (!words.test($(this).val())) {
			$( this ).val('').fadeOut('slow', function() {
				$( this ).css('background-color', '#fff0f0');
				$( this ).css('border-color', 'red');
				$( this ).fadeIn('slow', function() {
					$( this ).animate({ borderColor: '#CCC', backgroundColor: '#fff8c0' }, 'slow' );
				});
			});
		}
	});
	$( "#PlayerSearch" ).autocomplete({
		source: function( request, response ) {
			$.getJSON(
				"<?=HTTP_SERVICE ?>Search/SearchService.php",
				{
					Action: 'Search/Player',
					type: 'all',
					search: request.term,
					limit: 6
				},
				function( data ) {
					var suggestions = [];
					$.each(data, function(i, val) {
						suggestions.push({label: (val.Persona.length>0?val.Persona:"<i>No Persona</i>") + " (" + val.KAbbr + ":" + val.PAbbr + ")", value: val.MundaneId });
					});
					response(suggestions);
				}
			);
		},
		focus: function( event, ui ) {
			return showLabel('#PlayerSearch', ui);
		}, 
		delay: 1000,
		minLength: 3,
		select: function (e, ui) {
			showLabel('#PlayerSearch', ui);
			document.location.href = '?Route=Player/index/' + ui.item.value;
			return false;
		}
	});
	$( "#PlayerSearch" ).keydown( function () {
        $('#PlayerSearch').autocomplete('option', 'delay', Math.max(100, 900 / ($('#PlayerSearch').val().length + 1)) );
    });
	$('form').submit(function() {
		var go = true;
		$(this).find('.required-field').each(function(k, field) {
			if ($(this).val().trim().length < 1) {
				$(this).fadeOut('slow', function() {
					$( this ).css('background-color', '#ffc0c0');
					$( this ).css('border-color', 'red');
					$( this ).fadeIn('slow', function() {
						$( this ).animate({ borderColor: '#CCC', backgroundColor: '#fff8c0' }, 'slow' );
					});
				});
				go = false;
			}
		});
		return go;
	});
	$('.required-field').blur(function() {
		if ($(this).val().trim().length < 1) {
			$(this).fadeOut('slow', function() {
				$( this ).css('background-color', '#ffc0c0');
				$( this ).css('border-color', 'red');
				$( this ).fadeIn('slow', function() {
					$( this ).animate({ borderColor: '#CCC', backgroundColor: '#fff8c0' }, 'slow' );
				});
			});
			return false;
		}
	});
	$( "#ParkSearch" ).autocomplete({
		source: function( request, response ) {
			$.getJSON(
				"<?=HTTP_SERVICE ?>Search/SearchService.php",
				{
					Action: 'Search/Park',
					name: request.term,
					limit: 6
				},
				function( data ) {
					var suggestions = [];
					$.each(data, function(i, val) {
						suggestions.push({label: val.Name, value: val.ParkId });
					});
					response(suggestions);
				}
			);
		},
		focus: function( event, ui ) {
			return showLabel('#ParkSearch', ui);
		}, 
		delay: 1000,
		minLength: 2,
		select: function (e, ui) {
			showLabel('#ParkSearch', ui);
			document.location.href = '?Route=Park/index/' + ui.item.value;
			return false;
		}
	});
	$( "#ParkSearch" ).keydown( function () {
        $('#ParkSearch').autocomplete('option', 'delay', Math.max(100, 900 / ($('#ParkSearch').val().length + 1)) );
    });
});
