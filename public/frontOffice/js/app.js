const x=document.getElementsByClassName("__category");
const y=document.getElementById('categories');
const z=document.getElementsByClassName("id_category");
const a=document.getElementsByClassName("prod");
for (let i = 0; i < y.children.length; i++) {






    y.children[i].addEventListener('change', (event) => {


        if (event.currentTarget.checked) {
            for (let j = 0; j < y.children.length; j++) {
                console.log(y.children[j].checked);
                if(y.children[j].checked==true){
                    const ids=document.getElementsByClassName(y.children[j].value);
                    console.log(ids);
                    for (var k = 0; k < ids.length; k++) {
                        ids.item(k).hidden=false;
                    }
                }
                else
                {
                    const ids=document.getElementsByClassName(y.children[j].value);
                    for (var k = 0; k < ids.length; k++) {
                        ids.item(k).hidden=true;
                    }
                }


            }

        } else {
            let test=0;

            for (let j = 0; j < y.children.length; j++) {
                if(y.children[j].checked==true){
                    test=1;
                    const ids=document.getElementsByClassName(y.children[j].value);
                    for (var k = 0; k < ids.length; k++) {
                        ids.item(k).hidden=false;
                    }
                }
                else
                {
                    const ids=document.getElementsByClassName(y.children[j].value);
                    for (var k = 0; k < ids.length; k++) {
                        ids.item(k).hidden=true;
                    }
                }

                if(test==0){
                    const ids=document.getElementsByClassName(y.children[j].value);
                    for (var k = 0; k < ids.length; k++) {
                        ids.item(k).hidden=false;
                    }
                }

            }
            console.log(y.children[i].value)
        }
    })
}

$.ajax({
    type:'POST',
    url:'http://127.0.0.1:8000/searchclasse',
    data:{
        name:y.children[j].value,
    },
    success:function(data){
        $("#prods").html(data);
    }
});


$("#myTable tr").filter(function() {
    $(this).toggle($(this).text().toLowerCase().indexOf(y.children[j].value  ) > -1)
});