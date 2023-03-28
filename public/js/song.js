window.onload=()=>{
    //gestion des boutons delete
    let links = document.querySelectorAll("[data-delete]")

    //faire une boucle sur links
    for(link of links){
        // écouter le click
        link.addEventListener("click", function(e){
            // empêcher la navigation
            e.preventDefault()
            // demander confirmation
            if(confirm("Voulez vous supprimer cette musique ?")){
                // envoyer une requête Ajax vers le href du lien avec la méthode DELETE
                fetch(this.getAttribute("href"),{
                    method: "DELETE",
                    headers:{ //information à envoyer en entête
                        "X-Requested-With": "XMLHttpRequest",//Ajax pour ne pas devoir initialiser l'objet
                        "Content-Type": "application/json" // Pour donner le type d'envoi en l'occurence du json
                    },
                    body: JSON.stringify({'_token': this.dataset.token}) //donnée: un tableau à convertir en json
                }).then(
                    //récupérer la réponse en json
                    response => response.json()
                ).then(data => {
                    if(data.success)
                        this.parentElement.remove()
                    else
                        alert(data.error)
                }).catch(e => alert(e))// au cas la promesse n'est pas tenue
            }
        })
    }

}