# Club Ethical Hacking - CLUSIR-Rhone-Alpes

## 2019-11-27

### Installation

1. installer `docker` et `docker-compose`: 
    - https://docs.docker.com/install
    - https://docs.docker.com/compose/install/
2. cloner ce dépôt:
    - `git clone https://github.com/Club-Ethical-Hacking-CLUSIR-Rhone-Alpes/2019-11-27.git`
3. rendez-vous dans le répertoire du projet, puis dans `challenge/server` pour executer les commandes suivante:
    - `chmod 777 -R status`
    - `docker-compose build`

Vous pouvez désormais lancer le challenge: `docker-compose up`

> `docker-compose down` OU `ctrl+c` pour l'arrêter

---

### Pré-requis

- Un OS type Kali Linux
    - Image VirtualBox: https://www.offensive-security.com/kali-linux-vm-vmware-virtualbox-image-download/
    - Sous-système Windows: https://www.kali-linux.fr/installation/faire-tourner-kali-linux-en-tant-que-sous-systeme-de-windows-10

- Un logiciel d’analyse de PCAP (Wireshark, Networkminer)
    - https://www.wireshark.org/#download

- Metasploit (déjà installé sur Kali Linux)
    - https://github.com/rapid7/metasploit-framework/wiki/Nightly-Installers

- Nmap  (déjà installé sur Kali Linux)
    - https://nmap.org/download.html

### Objectifs

> *Les fichiers de présentations (powerpoint, pdf) sont disponibles dans le dossier `documents`*

La société OneSmile Inc. vous a recruté pour réaliser un pentest sur un addon de leur application la plus populaire. Cet addon est utilisé pour du tracking utilisateur. 

Le périmètre de la prestation est UNIQUEMENT sur l’addon (sous forme de binaire) et sur les ressources directement associées (api, librairies, etc.)


L'exécutable est disponible dans `challenge/client/track-fantastic.exe`

Votre objectif est de tester la sécurité de cet addon et d’évaluer le risque pouvant être porté à OneSmile Inc. en cas d’attaque.

> une partie de ce challenge est à réaliser en ligne.

> il est nécessaire d'avoir réalisé la partie "installation" pour pouvoir mener à bien le challenge.
