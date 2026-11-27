#!/bin/bash
cd "$(dirname "$0")"

echo "=== Memeriksa status repositori ==="
git status

echo "=== Memastikan branch adalah main ==="
git branch -M main

echo "=== Menjalankan git push ke GitHub ==="
git push -u origin main
