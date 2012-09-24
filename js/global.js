window.addEventListener('DOMContentLoaded', function(e){
    document.getElementById('type').addEventListener('change', function(e){
        if(this.value == 'Own')
        {
            document.querySelector('div.own-type').style.display = 'block';
        } else {
            document.querySelector('div.own-type').style.display = 'none';
        }
        // Show extra options for specific field types:
        var elems = document.querySelectorAll('fieldset.type');
        for(var i=0; i<elems.length; i++)
        {
            elems[i].style.display = 'none';
        }
        document.querySelector('fieldset.' + this.value.toLowerCase()).style.display = 'block';
    });
});