FROM postgres:latest
# Set environment variables for PostgreSQL
ENV POSTGRES_USER=camagru_user
ENV POSTGRES_PASSWORD=camagru_password
ENV POSTGRES_DB=camagru_db

# Expose the PostgreSQL port
EXPOSE 5432 
# Copy initialization scripts
COPY pg-init.sql /docker-entrypoint-initdb.d/
# Set the default command to run PostgreSQL
CMD ["postgres"]