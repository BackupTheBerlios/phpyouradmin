/* auteur: Artec-VM */
/* Date de cr�ation: 19/11/2002 */

function setSelectOptions(the_form, the_select, do_check)
{
    var selectObject = document.forms[the_form].elements[the_select];
    var selectCount  = selectObject.length;

    for (var i = 0; i < selectCount; i++) {
        selectObject.options[i].selected = do_check;
    } // end for

    return true;
} // end of the 'setSelectOptions()' function

// fonction trim eq php
function trim (aString) {
	return aString.replace(/^\s+/g,'').replace(/\s+$/g,'');
}
