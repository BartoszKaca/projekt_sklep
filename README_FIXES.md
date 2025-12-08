# 🔧 SZYBKA NAPRAWA BŁĘDÓW - README

## 🎯 Co zostało naprawione?

1. **Błąd 500 - "_oldStatus"** → OrderObserver.php
2. **Błąd raportu inwentarza** → ReportController.php  
3. **phpMyAdmin nie działa** → nginx/default.conf

## 🚀 Szybkie wdrożenie (3 minuty)

### Jedna komenda (z lokalnej maszyny):

```bash
# Wgraj pliki i uruchom naprawę
cd /Users/kaca/Documents/projekt_sklep/sklep

# Upload wszystkich plików jedną komendą
scp app/Observers/OrderObserver.php \
    app/Http/Controllers/Admin/ReportController.php \
    docker/nginx/conf.d/default.conf \
    quick-fix.sh \
    root@bartoszkaca.online:/tmp/

# Uruchom naprawę na serwerze
ssh root@bartoszkaca.online << 'ENDSSH'
cd /root/projekt_sklep/sklep
mv /tmp/OrderObserver.php app/Observers/
mv /tmp/ReportController.php app/Http/Controllers/Admin/
mv /tmp/default.conf docker/nginx/conf.d/
mv /tmp/quick-fix.sh .
chmod +x quick-fix.sh
./quick-fix.sh
ENDSSH
```

### Lub krok po kroku:

```bash
# 1. Upload plików
scp app/Observers/OrderObserver.php root@bartoszkaca.online:/root/projekt_sklep/sklep/app/Observers/
scp app/Http/Controllers/Admin/ReportController.php root@bartoszkaca.online:/root/projekt_sklep/sklep/app/Http/Controllers/Admin/
scp docker/nginx/conf.d/default.conf root@bartoszkaca.online:/root/projekt_sklep/sklep/docker/nginx/conf.d/
scp quick-fix.sh root@bartoszkaca.online:/root/projekt_sklep/sklep/

# 2. SSH i uruchom
ssh root@bartoszkaca.online
cd /root/projekt_sklep/sklep
chmod +x quick-fix.sh
./quick-fix.sh
```

## ✅ Test po wdrożeniu

```bash
# Z lokalnej maszyny
./test-fixes.sh bartoszkaca.online
```

Lub manualnie sprawdź:
- http://bartoszkaca.online/pma (login: root/root)
- http://bartoszkaca.online/admin/reports/inventory
- http://bartoszkaca.online/admin/products/1/stock

## 📚 Dokumentacja

- **QUICK_FIX_GUIDE.md** - szczegółowy przewodnik
- **FIXES_COMPLETE.md** - pełna dokumentacja napraw
- **DEPLOYMENT_CHECKLIST.md** - checklist wdrożenia

## ⚠️ Ważne

- Backup jest tworzony automatycznie przez `quick-fix.sh`
- Zero downtime (restart tylko Nginx)
- Nie wymaga migracji bazy danych
- Wszystkie zmiany są backward-compatible

## 🔄 Rollback

```bash
ssh root@bartoszkaca.online
cd /root/projekt_sklep/sklep
git checkout app/Observers/OrderObserver.php
git checkout app/Http/Controllers/Admin/ReportController.php
git checkout docker/nginx/conf.d/default.conf
docker-compose -f docker-compose.prod.yml restart
```

## 📞 Troubleshooting

Jeśli coś nie działa:

```bash
# Sprawdź logi
ssh root@bartoszkaca.online
docker logs sklep_app --tail=100
docker logs sklep_nginx --tail=100
docker exec sklep_app tail /var/www/html/storage/logs/laravel.log
```

---

**Status:** ✅ Gotowe do wdrożenia  
**Czas wdrożenia:** ~3 minuty  
**Ryzyko:** Niskie  
**Data:** 08.12.2025
