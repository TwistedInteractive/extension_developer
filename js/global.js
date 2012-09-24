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

    // For debugging:
    document.getElementById('testdata').addEventListener('click', function(e){
        e.preventDefault();
        var elems = document.querySelectorAll('input[type=text]');
        for(var i=0; i<elems.length; i++)
        {
            elems[i].value = elems[i].getAttribute('data-debug');
        }
        // Custom delegates:
        var elems = document.querySelectorAll('label.delegate input');
        for(var i=0; i<elems.length; i++)
        {
            if(Math.random() < .1) { elems[i].checked = true; }
        }

    });
});