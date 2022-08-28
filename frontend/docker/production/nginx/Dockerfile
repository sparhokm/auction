FROM node:18-alpine as builder

ENV REACT_APP_AUTH_URL=%%REACT_APP_AUTH_URL%%

WORKDIR /app

COPY ./package.json ./yarn.lock ./
RUN yarn install && yarn cache clean

COPY ./ ./
RUN yarn build

FROM nginx:1.21-alpine

COPY ./docker/production/nginx/conf.d /etc/nginx/conf.d
COPY ./docker/production/nginx/docker-entrypoint-env.sh /
RUN chmod +x /docker-entrypoint-env.sh

ENTRYPOINT ["/docker-entrypoint-env.sh"]
CMD ["nginx", "-g", "daemon off;"]

WORKDIR /app

COPY --from=builder /app/build ./public
