function ValidateEdit()
{
    console.log('Validating...');

    try
    {
        var fn = document.getElementById('fn').value;
        console.log('Validating...');
        var ln = document.getElementById('ln').value;
        console.log('Validating...');
        var ema = document.getElementById('ema').value;
        var hd = document.getElementById('hd').value;
        var sum = document.getElementById('sum').value;
        var n = ema.indexOf("@");

        if(fn==null || fn=="" || ln==null || ln=="" || ema==null || ema=="" || hd==null || hd=="" || sum==null || sum=="")
        {
        	alert("All fields are required");
        	return false;
        }
        if (n==-1)
        {
        alert("Email address must contain @");
        return false;
        }

        return true;
    }

    catch(e)
    {
        return false;
    }

    return false;
}


function validateLogIn()
{
    console.log('Validating...');
    try
    {
        pw = document.getElementById('id_1723').value;
        nm = document.getElementById('email').value;
        var n = nm.indexOf("@");
        console.log("Validating pw and email"+pw);
        if(nm==null || n==-1)
        {
        	alert("Invalid email address");
        	return false;
        }
        if (pw == null || pw == "")
        {
        alert("Both fields must be filled out");
        return false;
        }

        return true;
    }
    catch(e)
    {
        return false;
    }

    return false;
}

// utility that creates the position boxes in html
function createPosBox ()
{
    countPos = 0;
    $(document).ready(function()
    {
      window.console && console.log('Document ready called');
      $('#addPos').click(function(event)
      {
        event.preventDefault();
        if ( countPos >= 9 )
        {
          alert("Maximum of nine position entries exceeded");
          return;
        }
        countPos++;

        window.console && console.log("Adding position "+countPos);
        $('#position_fields').append(
        '<div id="position'+countPos+'"><p>Year: <input type="text" name="year'+countPos+'" value="" /><input type="button" value="-" onclick="$(\'#position'+countPos+'\').remove();return false;"></p><textarea name="desc'+countPos+'" rows="8" cols="80"> </textarea></div>');
      });
    });
}

function eduSearch()
{
    $( document ).ready(function() {
        $(".school").autocomplete({
		     source: "school.php",
		     minLength: 1
	       });
    });
}

// utility that creates the education boxes in html
function createEduBox()
{
    countEdu = 0;
    $(document).ready(function()
    {
      window.console && console.log('Document ready called');
      $('#addEdu').click(function(event){
        event.preventDefault();
        if ( countEdu >= 9 ) {
            alert("Maximum of nine education entries exceeded");
            return;
        }
        countEdu++;
        window.console && console.log("Adding education "+countEdu);

        $('#edu_fields').append(
            '<div id="edu'+countEdu+'"> \
            <p>Year: <input type="text" name="edu_year'+countEdu+'" value="" /> \
            <input type="button" value="-" onclick="$(\'#edu'+countEdu+'\').remove();return false;"><br>\
            <p>School: <input type="text" size="80" name="edu_school'+countEdu+'" class="school" value="" />\
            </p></div>');

            eduSearch();
        });
    });
}
