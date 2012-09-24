window.addEventListener('DOMContentLoaded', function(e){
    document.getElementById('type').addEventListener('change', function(e){
        if(this.value == 'Own')
        {
            document.querySelector('div.own-type').style.display = 'block';
        } else {
            document.querySelector('div.own-type').style.display = 'none';
        }
    });
});