# Magento CPROSP

*CPROSP* è l'acronimo di **C**atalog **P**rice **R**ules **O**ver **S**pecial **P**rice.

Questo modulo modifica il comportamento del CatalogPriceRules quando interagisce con un prodotto che ha uno Special Price assegnato.

Da usare con **Magento fino alla versione 1.8.x**

**Il problema:**

Le regole di sconto assegnate tramite CatalogPriceRules non vengono applicate se il prezzo assegnato tramite special price è inferiore al prezzo elaborato scontato.

**Soluzione**

E' necessario purtroppo fare una modifica al core di Magento e a tale scopo ho creato questo modulo.


**ATTENZIONE**

Dato un prodotto avente uno *Special Price* definito, lo sconto applicato dalla regola del CatalogPriceRule verrà calcolato sulla base dello *Special Price* e non del prezzo regolare.

Ad esempio

Un prodotto avente:
* **prezzo base** € 25.00;
* **prezzo speciale** €23.00;
* **regola prezzo catalogo** che impone uno sconto dell'80% a tutti i prodotti del catalogo

Il prezzo finale sarà di € 4.60 e non di € 5.00 in quanto l'80% viene sottratto dallo special price piuttosto che dal prezzo base.

*Nota*

La soluzione del problema è stata fornita da diversi utenti su [StackOverflow](http://stackoverflow.com/questions/18120342/catalog-price-rules-applied-to-special-price), 
non ho fatto altro che pacchettizzare e trasformare in modulo il codice da loro fornito in modo da facilitarne l'installazione. 

Thx StackOverflow!


##Installazione

Scaricare il repository ZIP e scompattarlo nella propria root di Magento

*oppure*

questo modulo è installabile attraverso *composer* aggiungendo le seguenti righe al proprio composer.json

```js
{
    "require":[
        ...
        "vmasciotta/magento-cprosp":"1.8"
        ...
    ],
    "repositories":[
        ...
        {"type": "vcs", "url":"https://github.com/vmasciotta/magento-cprosp.git"}
        ...
    ]
}
```
