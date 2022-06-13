CREATE TABLE USERS (
    id SERIAL NOT NULL PRIMARY KEY,
    username VARCHAR(20) NOT NULL,
    password_hash VARCHAR(255) NOT NULL
);


CREATE TABLE AGENDAS (
    id SERIAL NOT NULL PRIMARY KEY,
    owner_id INTEGER NOT NULL,
    name VARCHAR(20) NOT NULL
    
);

CREATE TABLE ACCOUNTS (
    id SERIAL NOT NULL PRIMARY KEY,
    user_id INTEGER NOT NULL UNIQUE,
    last_name VARCHAR(20) NOT NULL,
    first_name VARCHAR(20) NOT NULL,
    email VARCHAR(255) NOT NULL,
    creation_date TIMESTAMP WITHOUT TIME ZONE NOT NULL,
    last_agenda INTEGER,
    FOREIGN KEY (user_id) REFERENCES USERS (id) ON UPDATE CASCADE ON DELETE CASCADE,
    
    
);

ALTER TABLE ACCOUNTS ADD CONSTRAINT FK_ACCOUNTS_AGENDAS FOREIGN KEY (last_agenda) REFERENCES AGENDAS (id) ON UPDATE CASCADE ON DELETE SET NULL;
ALTER TABLE AGENDAS ADD CONSTRAINT FK_AGENDAS_OWNER_ID FOREIGN KEY (owner_id) REFERENCES ACCOUNTS (id) ON UPDATE CASCADE ON DELETE CASCADE;

CREATE TABLE GROUPS (
    id SERIAL NOT NULL PRIMARY KEY,
    owner_id INTEGER NOT NULL,
    name VARCHAR(20) NOT NULL,
    FOREIGN KEY (owner_id) REFERENCES ACCOUNTS (id) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE GROUP_TO_ACCOUNTS (
    group_id INTEGER NOT NULL,
    member_id INTEGER NOT NULL,
    FOREIGN KEY (group_id) REFERENCES GROUPS (id) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (member_id) REFERENCES ACCOUNTS (id) ON UPDATE CASCADE ON DELETE CASCADE,
    PRIMARY KEY (group_id, member_id)
);


CREATE TABLE USER_TOKENS (
    id SERIAL NOT NULL PRIMARY KEY,
    token VARCHAR(255) NOT NULL,
    expiry TIMESTAMP WITHOUT TIME ZONE NOT NULL,
    user_id INT NOT NULL,
    CONSTRAINT fk_user_id FOREIGN KEY (user_id) REFERENCES USERS (id) ON DELETE CASCADE
);

CREATE TABLE EVENTS (
    id SERIAL NOT NULL PRIMARY KEY,
    startTS TIMESTAMP WITHOUT TIME ZONE NOT NULL,
    endTS TIMESTAMP WITHOUT TIME ZONE NOT NULL,
    export TIMESTAMP WITHOUT TIME ZONE NOT NULL,
    owner_id INTEGER NOT NULL,
    title VARCHAR(50) NOT NULL,
    description VARCHAR(255) NOT NULL,

    FOREIGN KEY (owner_id) REFERENCES ACCOUNTS (id) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE GROUP_TO_AGENDAS (
    agenda_id INTEGER NOT NULL,
    group_id INTEGER NOT NULL,

    FOREIGN KEY (agenda_id) REFERENCES AGENDAS (id) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (group_id) REFERENCES GROUPS (id) ON UPDATE CASCADE ON DELETE CASCADE,
    PRIMARY KEY (agenda_id, group_id)

);

CREATE TABLE ACCOUNT_TO_AGENDAS (
    agenda_id INTEGER NOT NULL,
    account_id INTEGER NOT NULL,

    FOREIGN KEY (agenda_id) REFERENCES AGENDAS (id) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (account_id) REFERENCES ACCOUNTS (id) ON UPDATE CASCADE ON DELETE CASCADE,
    PRIMARY KEY (agenda_id, account_id)
);


CREATE TABLE EVENTS_TO_AGENDAS (
    event_id INTEGER NOT NULL,
    agenda_id INTEGER NOT NULL,

    FOREIGN KEY (event_id) REFERENCES EVENTS (id) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (agenda_id) REFERENCES AGENDAS (id) ON UPDATE CASCADE ON DELETE CASCADE,
    PRIMARY KEY (event_id, agenda_id)
);

INSERT INTO
    USERS (username, password_hash)
VALUES
    ('admin', 'admin');

INSERT INTO
    ACCOUNTS(
        user_id,
        last_name,
        first_name,
        email,
        creation_date,
        banner_url,
        logo_url,
        language,
        theme
    )
VALUES
    (
        1,
        'dupont',
        'jean',
        'dupont.jean@gmail.com',
        '2020-05-07 00:00:00',
        'dz',
        'dz',
        'Fr_fr',
        'dark'
    );

INSERT INTO
    ACCOUNTS(
        user_id,
        last_name,
        first_name,
        email,
        creation_date,
        banner_url,
        logo_url,
        language,
        theme
    )
VALUES
    (
        1,
        'dupont',
        'louis',
        'dupont.louis@gmail.com',
        '2020-05-07 00:00:00',
        'dz',
        'dz',
        'Fr_fr',
        'dark'
    );

INSERT INTO
    KEYWORDS(name)
VALUES
    ('accounts');

Select
    id
from
    Keywords
where
    name = 'Ms Jan'
SELECT
    TRANSLATE.value
FROM
    TRANSLATE
    INNER JOIN KEYWORDS ON KEYWORDS.id = TRANSLATE.keyword_id
WHERE
    KEYWORDS.name = ?
    INNER JOIN LANGUAGE ON LANGUAGE.id = TRANSLATE.language_id
WHERE
    LANGUAGE.name = ?;