
# Stage 1: Build aplikasi Vue menggunakan Node.js
FROM node:24-bookworm-slim AS build

WORKDIR /app

# Salin file dependencies terlebih dahulu
COPY package.json package-lock.json ./

# Install dependencies sesuai package-lock.json
RUN npm ci

# Salin source code frontend
COPY . .

# Alamat API yang digunakan browser
ARG VITE_API_URL=/api
ENV VITE_API_URL=${VITE_API_URL}

# Build Vue menjadi file statis
RUN npm run build

# Stage 2: Jalankan hasil build menggunakan Nginx
FROM nginx:1.28-alpine

# Gunakan konfigurasi Nginx khusus PlaceLog
COPY nginx.conf /etc/nginx/conf.d/default.conf

# Salin hasil build Vue, bukan node_modules
COPY --from=build /app/dist /usr/share/nginx/html

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]
