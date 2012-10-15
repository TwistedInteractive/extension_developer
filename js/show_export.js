window.addEventListener('DOMContentLoaded', function(e){
    // Run that code!
    prettyPrint();
    var items = document.querySelectorAll('ul.accordeon li.file');
    for(var i=0; i<items.length; i++)
    {
        bindCollapse(items[i]);
    }
    // Re-openen the opened tabs:
    var opened = window.localStorage.getItem('opened');
    if(opened != null)
    {
        opened = opened.split(',');
        for(var i=0; i<opened.length; i++)
        {
            document.querySelector('#' + opened[i] + ' div.code').classList.add('expanded');
        }
    }
});

function bindCollapse(elem)
{
    elem.querySelector('h4').addEventListener('click', function(e){
        elem.querySelector('div.code').classList.toggle('expanded');
        var opened = document.querySelectorAll('div.code.expanded');
        var ids = [];
        for(var i=0; i<opened.length; i++)
        {
            ids.push(opened[i].parentNode.id);
        }
        window.localStorage.setItem('opened', ids);
    });
}