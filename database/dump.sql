CREATE TABLE IF NOT EXISTS users
(
    id          int auto_increment
        primary key,
    name        varchar(100) not null,
    webauthn_id varchar(32)  not null,
    password    varchar(64)  not null,
    constraint webauth_uniq_id
        unique (webauthn_id)
);

CREATE TABLE IF NOT EXISTS users_webauthn_credentials
(
    id                int auto_increment
        primary key,
    credential_id     varchar(128) not null,
    user_id           int          not null,
    publickey         text         not null,
    signature_counter varchar(100) null,
    constraint users_webauthn_credentials_users_id_fk
        foreign key (user_id) references users (id)
            on delete cascade
);

create index user_id
    on users_webauthn_credentials (user_id);