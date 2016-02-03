# Magento CPROSP

*CROSP* è l'acronimo di **C**atalog **P**rice **R**ules **O**ver **S**pecial **P**rice.

Questo modulo modifica il comportamento del CatalogPriceRules quando interagisce con un prodotto che ha uno Special Price assegnato.

Da usare con **Magento 1.9.x**

**Il problema:**

Le regole di sconto assegnate tramite CatalogPriceRules non vengono applicate se il prezzo assegnato tramite special price è inferiore al prezzo elaborato scontato.

**Soluzione**

E' necessario purtroppo fare una modifica al core di Magento e a tale scopo ho creato questo modulo.

*Nota*
La soluzione del problema è stata fornita da diversi utenti su [stackoverflow](http://stackoverflow.com/questions/18120342/catalog-price-rules-applied-to-special-price),
non ho fatto altro che pacchettizzare e trasformare in modulo il codice da loro fornito in modo da facilitarne l'installazione.

Thx StackOverflow!
