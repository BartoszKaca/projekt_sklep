-- Inicjalizacyjny skrypt MySQL
-- Wykonywany tylko przy pierwszym uruchomieniu kontenera

-- Ustawienia charset
SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

-- Upewnij się, że baza istnieje z poprawnym charset
CREATE DATABASE IF NOT EXISTS sklep_laravel 
    CHARACTER SET utf8mb4 
    COLLATE utf8mb4_unicode_ci;

-- Info o konfiguracji
SELECT 'MySQL initialized with lower_case_table_names=2' AS status;
SELECT @@lower_case_table_names AS lower_case_table_names_value;

