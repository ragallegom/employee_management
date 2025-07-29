# Base image
FROM node:20-alpine

# Set working directory
WORKDIR /app

# Install dependencies
COPY package*.json ./
RUN npm install

# Copy source code
COPY . .

# Build app
RUN npm run build

# Serve with preview
RUN npm install -g serve
CMD ["serve", "-s", "dist", "-l", "4173"]
