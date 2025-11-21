@echo off
REM ================================================================
REM Database Import Script for Library_Uni
REM Run this to import the database
REM ================================================================

echo ========================================
echo Library_Uni - Database Import
echo ========================================
echo.

REM Check if database.sql exists
if not exist "database.sql" (
    echo ERROR: database.sql not found!
    echo Please run this script from the library_uni directory.
    pause
    exit /b 1
)

echo Found: database.sql
echo.

REM Try common MySQL paths
set MYSQL_PATH=

REM Check if mysql is in PATH
where mysql >nul 2>&1
if %ERRORLEVEL% EQU 0 (
    set MYSQL_PATH=mysql
    goto :found
)

REM Try XAMPP
if exist "C:\xampp\mysql\bin\mysql.exe" (
    set MYSQL_PATH=C:\xampp\mysql\bin\mysql.exe
    goto :found
)

REM Try WAMP
if exist "C:\wamp64\bin\mysql\mysql8.0.27\bin\mysql.exe" (
    set MYSQL_PATH=C:\wamp64\bin\mysql\mysql8.0.27\bin\mysql.exe
    goto :found
)

REM Try Program Files
if exist "C:\Program Files\MySQL\MySQL Server 8.0\bin\mysql.exe" (
    set MYSQL_PATH=C:\Program Files\MySQL\MySQL Server 8.0\bin\mysql.exe
    goto :found
)

echo ERROR: MySQL not found!
echo.
echo Please install MySQL or add it to your PATH.
echo Common locations:
echo   - C:\xampp\mysql\bin
echo   - C:\wamp64\bin\mysql\[version]\bin
echo   - C:\Program Files\MySQL\MySQL Server 8.0\bin
echo.
pause
exit /b 1

:found
echo Found MySQL: %MYSQL_PATH%
echo.
echo Importing database...
echo You will be prompted for your MySQL password.
echo.

"%MYSQL_PATH%" -u root -p < database.sql

if %ERRORLEVEL% EQU 0 (
    echo.
    echo ========================================
    echo SUCCESS! Database imported successfully!
    echo ========================================
    echo.
    echo Next step: Test the connection
    echo   php tests\test-db-connection.php
    echo.
) else (
    echo.
    echo ========================================
    echo ERROR! Import failed!
    echo ========================================
    echo.
    echo Common solutions:
    echo 1. Make sure MySQL password is correct
    echo 2. Check if MySQL server is running
    echo 3. Verify .env file has correct DB_PASS
    echo.
)

pause
