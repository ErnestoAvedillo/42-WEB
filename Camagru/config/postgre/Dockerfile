FROM postgres:latest

# Copy initialization scripts (environment variables will come from docker-compose.yml)
COPY pg-init.sql /docker-entrypoint-initdb.d/

# Expose the PostgreSQL port
EXPOSE 5432

# Set the default command to run PostgreSQL
CMD ["postgres"]