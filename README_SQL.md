```sql



```

Migration visitlog:

```sql
INSERT INTO rpgclubs.visitlog (id, dayvisit, ipv4, hits) SELECT id, dayvisit, ipv4, hits FROM rpgclubs_xussr.rpgcrf_clubs_visitlog; 
```