# Group Calendar
Calendarul nostru este creat special pentru prietenii și grupurile care iubesc să petreacă timp împreună și au nevoie de o soluție simplă și eficientă pentru a organiza ieșiri și evenimente sociale. Produsul răspunde nevoii de a simplifica procesul de planificare, eliminând confuzia generată de mesaje sau apeluri multiple pentru a stabili când și unde toată lumea este disponibilă.

Acesta este un calendar social care permite utilizatorilor să creeze și să partajeze rapid planuri de ieșire, să vadă cine este disponibil și să coordoneze activitățile de grup fără bătăi de cap. Astfel, prietenii pot petrece mai puțin timp organizând și mai mult timp bucurându-se de momentele împreună. Spre deosebire de alte aplicații de organizare, care sunt centrate pe muncă sau pe gestionarea sarcinilor personale, calendarul nostru pune accent pe conectarea socială și pe facilitarea organizării spontane sau planificate a întâlnirilor de grup.

Cu o interfață simplă și intuitivă, calendarul nostru oferă o experiență plăcută și fără stres pentru toți cei implicați. Este mai mult decât un simplu instrument de organizare – este o platformă care aduce prietenii mai aproape și facilitează planificarea evenimentelor comune, indiferent de loc sau moment.

Pentru a asigura o experiență simplă și accesibilă, am decis să utilizăm o arhitectură web clasică, bazată pe o structură client-server. Pe partea de server, PHP a fost utilizat pentru gestionarea logicii aplicației și comunicarea cu baza de date, în timp ce pe partea de client, HTML, CSS și JavaScript au fost folosite pentru interfața de utilizare și interacțiunile dinamice.

Arhitectura simplă a permis o dezvoltare eficientă și rapidă, însă limitările PHP și JavaScript în gestionarea unor funcționalități complexe, cum ar fi notificările în timp real, au fost evidente și vor necesita optimizări în viitor.

# User Stories
US1: Ca utilizator, doresc să primesc alerte stilizate pentru evenimentele viitoare, astfel încât să fiu notificat la timp.

US2: Ca utilizator, vreau ca agenda să fie aranjată într-un mod mai clar, pentru a găsi rapid informațiile despre evenimente.

US3: Ca dezvoltator, doresc să refactorizez codul pentru a-l face mai clar și mai ușor de întreținut.

US4: Ca utilizator, doresc ca aplicația să fie aranjată optim pentru telefonul mobil, pentru a avea o experiență fluidă.

US5: Ca administrator/utilizator, doresc să am un buton de ștergere pentru calendarele comune, pentru a păstra ordinea în aplicație.

US6: Ca utilizator, doresc să pot șterge evenimentele pe care le-am creat, dacă nu mai sunt relevante.

US7: Ca utilizator, vreau să pot șterge prieteni din lista mea, dacă nu mai doresc să colaborez cu ei.

US8: Ca utilizator, vreau ca aplicația să aibă un aspect modern și uniform pentru o experiență vizuală plăcută.

US9: Ca utilizator, doresc ca aplicația să fie lipsită de erori pentru a funcționa fluent.

# Link repository MDS
https://github.com/ghenpen/mds_proiect.git

1. Sinteză a produsului rezultat
Proiectul dezvoltat este o aplicație web pentru gestionarea calendarelor, care permite utilizatorilor să creeze, să modifice și să gestioneze evenimente, precum și să administreze liste de prieteni și coordonatori. Aplicația oferă o interfață web prin care utilizatorii pot accesa și edita datele lor, iar acestea sunt stocate într-o bază de date relațională.

Comparativ cu versiunea inițial propusă, produsul rezultat include o arhitectură clar definită, cu separarea responsabilităților în componente distincte (frontend, backend și bază de date), utilizând tehnologii web standard (HTML, CSS, PHP, MySQL). De asemenea, s-au îmbunătățit mecanismele de autentificare și gestionare a prietenilor, facilitând colaborarea între utilizatori.

2. Descriere folosind diagrame C4
Nivel 1 – Context
Actor principal: Utilizatorul
Sistemul: Aplicația Web Calendar
Interacțiuni:

Utilizatorul interacționează cu aplicația web pentru a organiza și gestiona evenimente și prieteni.
Aplicația comunică cu baza de date pentru a stoca și a prelua informațiile utilizatorului.
Principalele componente implicate:

Aplicație Web – interfața principală accesibilă printr-un browser.
Bază de date MySQL – stochează informațiile despre utilizatori, evenimente și relațiile dintre aceștia.
Server Apache + PHP – gestionează logica de procesare a cererilor.


Diagrama Context
![alt text](https://github.com/unibuc-cs/Ford-Falcon/blob/master/Diagrams/Context.bmp)


Nivel 2 – Containere
Această diagramă detaliază arhitectura aplicației prin împărțirea sistemului în mai multe containere:

Web Browser – interfața utilizatorului (HTML, CSS, JavaScript)
Apache + PHP Server – backend-ul care procesează cererile HTTP și interacționează cu baza de date.
MySQL Database – gestionează datele persistente (utilizatori, evenimente, prieteni).
CSS Files – stilizarea interfeței utilizatorului.


Diagrama Container
![alt text](https://github.com/unibuc-cs/Ford-Falcon/blob/master/Diagrams/Container.bmp)


Nivel 3 – Componente
Această diagramă prezintă structura internă a aplicației și componentele sale software:

Authentication Handler – se ocupă de autentificarea și autorizarea utilizatorilor.
User Manager – gestionează informațiile contului utilizatorilor.
Event Manager – permite crearea, editarea și ștergerea evenimentelor.
Friend Manager – administrează lista de prieteni și cererile de prietenie.
Landing Page, Login Page, Signup Page, Calendar Page, Friends List – paginile HTML accesibile utilizatorilor.
Fiecare componentă din backend comunică cu baza de date prin interogări SQL pentru a prelua sau modifica informațiile.


Diagrama Component
![alt text](https://github.com/unibuc-cs/Ford-Falcon/blob/master/Diagrams/Component.bmp)


Diagrama C4
![alt text](https://github.com/unibuc-cs/Ford-Falcon/blob/master/Diagrams/DiagramaC4.bmp)


3. Cerințe Non-Funcționale și Soluții Arhitecturale
Cerință Non-FuncționalăSoluție Implementată
Scalabilitate - Arhitectura modulară permite adăugarea de noi funcționalități fără impact major asupra sistemului.
Performanță - Optimizarea interogărilor SQL și utilizarea caching-ului pentru încărcare rapidă a datelor.
Securitate - Autentificare utilizând hashing pentru parole și protecție împotriva SQL Injection.
Ușurință în utilizare - Interfață web intuitivă și organizare clară a paginilor și funcționalităților.
Disponibilitate - Server Apache configurat pentru a suporta multiple conexiuni simultane.
Portabilitate - Aplicația poate fi accesată de pe orice dispozitiv cu browser web.


4. Concluzie
Aplicația Web Calendar urmează o arhitectură clară, cu separare între frontend, backend și bază de date. Folosind tehnologiile web standard (Apache, PHP, MySQL), asigură o gestionare eficientă a evenimentelor și relațiilor între utilizatori. Arhitectura modulară permite extinderea ușoară a sistemului, iar măsurile de securitate asigură protecția datelor utilizatorilor.


# Diagrame
diagrama ER
![340330806-bae951f5-12b6-420b-986f-88bbf966d83f](https://github.com/user-attachments/assets/06213e17-fe07-4999-a10e-8833a0034654)
diagrama UML
![340330856-9365db00-6ad5-41f5-b0a7-7f79678aa25f](https://github.com/user-attachments/assets/b744a506-b9a2-4214-98ef-b13e72ae5eb2)
diagrama de workflow
![340330887-3ac11540-a59d-4958-9b50-193ad93676ba](https://github.com/user-attachments/assets/d263aef6-c987-466d-841e-d36f573aee60)
diagrama workflouw-ului de cod
![340330918-9de5e60f-9320-43c1-8b3e-e021ad544494](https://github.com/user-attachments/assets/e7a037e9-f857-4480-aa06-de19ac4a3ee6)

