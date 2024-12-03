Vue.createApp({
    data() {
        return { 
            map: null, // Référence à la carte Leaflet
            Inventaire: [], // Liste des objets récupérés
            Objets_marqueurs: [], // Liste des marqueurs et objets associés
            Objets_call: [], // Liste des objets récupérés depuis l'API
            txt_popup: "", // Texte affiché dans les popups
            data: null, // Données temporaires pour des requêtes d'API
            Markers: null, // Groupe de marqueurs pour la carte
            triche: false, // Mode triche activé ou non
            selectedObjet: null, // Objet actuellement sélectionné
            temps: 0, // Temps écoulé en minutes et secondes
            temps_seconde: 0, // Temps écoulé en secondes totales
            echec_deverrouillage: false, // Indicateur d'échec de déverrouillage
            essai: 0, // Nombre d'essais effectués
            score: 0, // Score du joueur
            fin: false, // Indique si le jeu est terminé
            date_debut: null, // Timestamp du début de la partie
            temps_ecoule: null, // Référence à l'intervalle pour le temps
            wmsLayer: null, // Couche WMS pour le mode triche
            penalite_triche: false // Indicateur de pénalité pour triche
        };
    },
    computed: {
        // Section vide pour des propriétés calculées si nécessaire
    },
    methods:{ 
        initialiserCarte() {
            // // console.log("Initialisation de la Carte ....");
            this.map = L.map('carte').setView([48.841, 2.5878], 15); // Initialisation de la carte
            L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}', {
                attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
                maxZoom: 18,
                id: 'mapbox/streets-v11',
                tileSize: 512,
                zoomOffset: -1,
                accessToken: 'pk.eyJ1Ijoid3liZyIsImEiOiJja3pwY3NkczEzZzgzMnBvMGRuMThyMmF2In0.4EWg3b0kAlgiARXr_S3qaQ'
            }).addTo(this.map);

            this.Markers = L.featureGroup().addTo(this.map); // Groupe de marqueurs ajouté à la carte
            this.objets_depart().then(() => { 
                this.ajout_marker(); 
                this.date_debut= Date.now(); // Stocke l'heure actuelle
                this.temps_ecoule = setInterval(this.gestion_temps, 1000); // Met à jour toutes les secondes

            });
        },

        objets_depart() {
            // console.log("Récupération des objets de départ ....");
            return fetch('https://paul-le-distrait.onrender.com/api/objets') // Appel API pour récupérer les objets
                .then(result => result.json())
                .then(result => { 
                    this.Objets_call = result.tableau; 
                    // console.log("Objets récupérés :", this.Objets_call);
                });
        },

        info_objet(id) {
            // console.log("Récupération de l'objet ....");
            return fetch('https://paul-le-distrait.onrender.com/api/objets/' + `${id}`) // Appel API pour récupérer un objet spécifique
                .then(result => result.json())
                .then(result => { 
                    this.data = result.objet[0]; 
                    // console.log("Données de l'objet :", this.data);
                });
        },

        async creation_marker(objet) {
            // console.log("Création d'un marker ....");
            const iconUrl = "../images/" + objet.img + ".png"; // URL de l'icône du marqueur
            // console.log("Taille de l'image :", objet.taille);
            const customIcon = L.icon({
                iconUrl: iconUrl,
                iconSize: objet.taille,
                iconAnchor: objet.position_ancre,
                popupAnchor: [0, -32]
            });
            const marker = L.marker(objet.geom.coordinates.reverse(), { icon: customIcon }); // Création du marqueur
            
            if (objet.objet_type !== "objet_recuperable") {
                const popupContent = await this.popup(objet); 
                marker.bindPopup(popupContent); // Ajout d'un popup si nécessaire
            };
            if (objet.objet_type === "bloque_objet") {
                objet.deverrouille = false;
                
            };

            marker.on('click', () => {
                this.Recuperation_objet(objet, marker); // Gestion du clic sur le marqueur
            });

            this.Objets_marqueurs.push({ marker: marker, objet: objet }); // Ajout à la liste des marqueurs
            // console.log("Marker créé :", { marker, objet });
        },

        ajout_marker() {
            // console.log("Ajout des markers ....");
            // console.log("Objets de départ :", this.Objets_call);
            this.Objets_call.forEach(objet => {
                this.creation_marker(objet); // Création des marqueurs pour chaque objet
            });
            // console.log("Liste des objets marqueurs :", this.Objets_marqueurs);
        },

        affiche_markers() {
            // console.log("Affichage des marqueurs ....");
            // console.log("Liste des objets marqueurs :", this.Objets_marqueurs);
            const currentZoom = this.map.getZoom(); // Zoom actuel ajusté
            this.Objets_marqueurs.forEach(element => {
                const minZoom = element.objet.minzoom;
                // console.log("Zoom actuel :", currentZoom);
                if (currentZoom >= minZoom) {
                    this.Markers.addLayer(element.marker); // Ajout du marqueur au groupe
                }
            });
            this.map.on('zoomend', () => {
                this.Objets_marqueurs.forEach(element => {
                    const minZoom = element.objet.minzoom;
                    const currentZoom = this.map.getZoom();
                    // console.log("Zoom actuel :", currentZoom, "Min zoom de l'objet :", minZoom);
                    if (currentZoom >= minZoom) {
                        this.Markers.addLayer(element.marker);
                    } else {
                        this.Markers.removeLayer(element.marker); // Retrait du marqueur si zoom insuffisant
                    }
                });
            });
        },

        async creation_marker(objet) {
            // Crée un marqueur personnalisé sur la carte pour un objet donné
            const iconUrl = "../images/" + objet.img + ".png";
            const customIcon = L.icon({
                iconUrl: iconUrl,
                iconSize: objet.taille, // Taille de l'icône
                iconAnchor: objet.position_ancre, // Ancrage de l'icône
                popupAnchor: [0, -32] // Position du popup par rapport à l'icône
            });

            const marker = L.marker(objet.geom.coordinates.reverse(), { icon: customIcon });
            
            if (objet.objet_type !== "objet_recuperable") {
                const popupContent = await this.popup(objet);
                marker.bindPopup(popupContent); // Ajout de contenu au popup si nécessaire
            }

            if (objet.objet_type === "bloque_objet") {
                objet.deverrouille = false; // Marqueur initialement verrouillé
            }

            marker.on('click', () => {
                this.Recuperation_objet(objet, marker); // Déclenche une action lors du clic
            });

            this.Objets_marqueurs.push({ marker: marker, objet: objet }); // Ajout à la liste des objets marqueurs
        },

        ajout_marker() {
            // Ajoute des marqueurs pour tous les objets récupérés depuis l'API
            this.Objets_call.forEach(objet => {
                this.creation_marker(objet);
            });
        },


        selection_objet(objet) {
            this.selectedObjet = objet; // Définit l'objet sélectionné
            // console.log("Objet sélectionné :", objet);
        },


        async popup(objet) {
            // console.log("Création du contenu du popup pour l'objet :", objet);
            let txt = "";
            if (objet.objet_type == "bloque_objet") {
                await this.info_objet(objet.objet_debloque).then(() => {
                    txt = `<b>Bloqué par : ${this.data.nom}</b><br>Indice : ${objet.indice}`;
                });
            }
            if (objet.objet_type == "bloque_code") {
                txt = `<b>Vous avez besoin d'un mot de passe 😢</b><br>Indice : ${objet.indice}`;
            }
            if (objet.objet_type == "objet_code") {
                txt = `<br>Mot de passe : ${objet.indice}`;
            }
            // console.log("Contenu du popup :", txt);
            return txt; // Retourne le contenu du popup
        },

        Recuperation_objet(objet, marker) {
            
            // Supprimer les anciens gestionnaires pour éviter les doublons
            marker.off('popupclose');
            
            // console.log("Récupération de l'objet :", objet);
            if (objet.objet_type == 'bloque_objet') {
                if (!objet.deverrouille) {
                    const popup = marker.getPopup();
                    if (popup) {
                        marker.once('popupclose', () => {
                            setTimeout(() => {
                                // console.log(objet);
                                
                                this.info_objet(objet.objet_debloque).then(() => {
                                    if (this.selectedObjet) {
                                        if (this.data.nom === this.selectedObjet.nom) {
                                            objet.deverrouille =true;
                                            this.info_objet(objet.objet_libere).then(() => {
                                                this.creation_marker(this.data).then(() => {
                                                // console.log(this.data);
                                                this.Markers.clearLayers();
                                                // console.log("Objets marqueurs avant affichage :", this.Objets_marqueurs);
                                                this.affiche_markers();});
                                            });
                                        }
                                    }
                                });
                            });
                        });
                    }
                }
            }
            if (objet.objet_type == 'bloque_code') {
                const popup = marker.getPopup();
                    if (popup) {
                        if (this.essai > 0 ) {
                            marker.once('popupclose', () => {
                                setTimeout(() => {
                                    let msg = prompt('Entrez le mot de passe correct :');
                                    this.info_objet(objet.objet_debloque).then(() => {
                                        // console.log(this.data);
                                        if (msg == this.data.indice) {
                                            alert('Félicitations vous avez réussi !!!');
                                            this.info_objet(objet.objet_libere).then(() => {
                                            this.creation_marker(this.data);
                                            this.Markers.clearLayers();
                                            this.affiche_markers();
                                        });
                                        
                                    } else {
                                        this.echec_deverrouillage = true;
                                        this.calcul_score(this.echec_deverrouillage);
                                        alert('Le mot de passe est incorrect 😢');
                                    }
                                }, 100);
                            });
                            }); 
                            
                        } else{
                            this.essai+= 1;
                        }
                        
                    };
            }

            if (objet.objet_type == 'objet_recuperable') {
                this.calcul_score(objet);
                this.Inventaire.push(objet); // Ajout à l'inventaire
                // console.log("Inventaire :", this.Inventaire);
                this.Markers.removeLayer(marker); // Suppression du marqueur
                this.Objets_marqueurs = this.Objets_marqueurs.filter(
                    (item) => item.objet.nom !== objet.nom
                );
                // console.log("Objets restants sur la carte :", this.Objets_marqueurs);
            }
        },
        gestion_temps(){
            const currentTime = Date.now(); // Heure actuelle
            const elapsedTime = currentTime - this.date_debut; // Temps écoulé en millisecondes
            this.temps = [Math.floor(elapsedTime / 60000),Math.floor((elapsedTime % 60000) / 1000)]; // Convertir en minutes et Reste converti en secondes
            this.temps_seconde = Math.floor(elapsedTime / 1000 )
        },

        calcul_score(action){
            if (action === true) {
                // retire des points en cas d'erreurs de mots de passe
                this.echec_deverrouillage = false;
                this.score += -20
            } else{ 
                if (this.fin) {
                    // Ajout de points bonus si le joueur termine en moins de 12 minutes
                    this.score += 900 - this.temps_seconde
                    alert(`Félicitations ! Votre score final est : ${this.score}`);
                    this.recuperation_score();
                    window.location.href = "/"; // redirection vers le hall of fame
                    
                } else {
                    // Ajout des points en fonction de l'importance du score
                    if (action.importance==='haute'){
                        clearInterval(this.temps_ecoule); // Arrête l'ecoulement du temps
                        this.score +=100;
                        this.fin = true;
                        this.calcul_score(false);

                    }
                    if (action.importance==='moyenne'){
                        this.score +=50;
                        
                    }
                    if (action.importance==='faible'){
                        this.score +=20;
                        
                    }
                    if (this.triche  && !this.penalite_triche) {
                        // Gestion de la pénalité de triche
                        this.score += -500;
                        this.penalite_triche=true;
                        
                    }
                }
            }

        },

        mode_triche() {
            // console.log("Mode triche :", this.triche ? "activé" : "désactivé");
            let wmsLayer = null;
            if (this.triche) {
                this.calcul_score(false)
                this.wmsLayer = L.tileLayer.wms('https://geoserver-002d.onrender.com/geoserver/MY_escape_game/ows?', {
                    layers: 'MY_escape_game:objets',  // Remplacez par le nom de votre couche WMS
                    styles: 'Carte_de_chaleur',
                    format: 'image/png',
                    transparent: true,
                    version: '1.1.1',
                    opacity: 0.6,
                    crs: L.CRS.EPSG4326
                }).addTo(this.map);
                // console.log("Mode triche activé, couche WMS ajoutée");
                // Logique pour activer le mode triche (exemple désactivé ici)
            } else {
                this.map.removeLayer(this.wmsLayer);
                this.penalite_triche=false;
                // console.log("Désactivation du mode triche.");
                // Logique pour désactiver le mode triche (exemple désactivé ici)
            }
        },

        recuperation_score(){
            // Envoie du score du joueur coté server
            // console.log(this.score);
            fetch('/logout', {
                method: 'POST',
                body: JSON.stringify({score : this.score}),
                headers: {
                    'Content-type': 'application/json'
                }

            })
            // .then(response => response.json())
            // .then(response => {
            //     console.log(response);
            // });
        },
    },
    mounted() {
        // console.log("Montage de l'application ....");
        this.initialiserCarte(); // Initialisation de la carte lors du montage
        this.affiche_markers(); // Affichage des marqueurs après le montage
    }
}).mount('#jeu');
