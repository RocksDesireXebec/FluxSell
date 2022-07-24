<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220723093150 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE api_token_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE categorie_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE choix_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE commande_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE panier_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE produit_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE reset_password_request_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE stripe_payment_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE utilisateur_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE api_token (id INT NOT NULL, utilisateur_id INT NOT NULL, token VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_7BA2F5EBFB88E14F ON api_token (utilisateur_id)');
        $this->addSql('CREATE TABLE categorie (id INT NOT NULL, categorie_id INT DEFAULT NULL, libelle VARCHAR(255) NOT NULL, description TEXT NOT NULL, capture VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_497DD634BCF5E72D ON categorie (categorie_id)');
        $this->addSql('CREATE TABLE choix (id INT NOT NULL, panier_id INT NOT NULL, produit_id INT DEFAULT NULL, date_choix TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, quantite INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_4F488091F77D927C ON choix (panier_id)');
        $this->addSql('CREATE INDEX IDX_4F488091F347EFB ON choix (produit_id)');
        $this->addSql('COMMENT ON COLUMN choix.date_choix IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE commande (id INT NOT NULL, panier_id INT NOT NULL, produit_id INT NOT NULL, quantite INT NOT NULL, date_achat TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, montant DOUBLE PRECISION NOT NULL, validite BOOLEAN NOT NULL, devise VARCHAR(255) NOT NULL, id_moyen_paiement INT NOT NULL, reference VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6EEAA67DF77D927C ON commande (panier_id)');
        $this->addSql('CREATE INDEX IDX_6EEAA67DF347EFB ON commande (produit_id)');
        $this->addSql('COMMENT ON COLUMN commande.date_achat IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE panier (id INT NOT NULL, proprietaire_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_24CC0DF276C50E4A ON panier (proprietaire_id)');
        $this->addSql('CREATE TABLE produit (id INT NOT NULL, categorie_id INT DEFAULT NULL, id_produit UUID NOT NULL, libelle VARCHAR(255) NOT NULL, prix DOUBLE PRECISION NOT NULL, etoile INT NOT NULL, marque VARCHAR(255) NOT NULL, etat INT NOT NULL, note DOUBLE PRECISION NOT NULL, qte_en_stock INT NOT NULL, description TEXT NOT NULL, informations_detaillees TEXT NOT NULL, capture VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_29A5EC27BCF5E72D ON produit (categorie_id)');
        $this->addSql('COMMENT ON COLUMN produit.informations_detaillees IS \'(DC2Type:array)\'');
        $this->addSql('CREATE TABLE reset_password_request (id INT NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, expires_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_7CE748AA76ED395 ON reset_password_request (user_id)');
        $this->addSql('COMMENT ON COLUMN reset_password_request.requested_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN reset_password_request.expires_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE stripe_payment (id INT NOT NULL, commande_id INT NOT NULL, stripe_token VARCHAR(255) NOT NULL, brand_stripe VARCHAR(255) NOT NULL, last4_stripe VARCHAR(255) NOT NULL, id_charge_stripe VARCHAR(255) NOT NULL, status_stripe VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, update_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_42EFB5F782EA2E54 ON stripe_payment (commande_id)');
        $this->addSql('COMMENT ON COLUMN stripe_payment.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN stripe_payment.update_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE utilisateur (id INT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, telephone VARCHAR(255) NOT NULL, pays_de_residence VARCHAR(255) NOT NULL, date_de_creation TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, is_verified BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1D1C63B3E7927C74 ON utilisateur (email)');
        $this->addSql('COMMENT ON COLUMN utilisateur.date_de_creation IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE api_token ADD CONSTRAINT FK_7BA2F5EBFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE categorie ADD CONSTRAINT FK_497DD634BCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE choix ADD CONSTRAINT FK_4F488091F77D927C FOREIGN KEY (panier_id) REFERENCES panier (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE choix ADD CONSTRAINT FK_4F488091F347EFB FOREIGN KEY (produit_id) REFERENCES produit (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67DF77D927C FOREIGN KEY (panier_id) REFERENCES panier (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67DF347EFB FOREIGN KEY (produit_id) REFERENCES produit (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE panier ADD CONSTRAINT FK_24CC0DF276C50E4A FOREIGN KEY (proprietaire_id) REFERENCES utilisateur (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC27BCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES utilisateur (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE stripe_payment ADD CONSTRAINT FK_42EFB5F782EA2E54 FOREIGN KEY (commande_id) REFERENCES commande (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE categorie DROP CONSTRAINT FK_497DD634BCF5E72D');
        $this->addSql('ALTER TABLE produit DROP CONSTRAINT FK_29A5EC27BCF5E72D');
        $this->addSql('ALTER TABLE stripe_payment DROP CONSTRAINT FK_42EFB5F782EA2E54');
        $this->addSql('ALTER TABLE choix DROP CONSTRAINT FK_4F488091F77D927C');
        $this->addSql('ALTER TABLE commande DROP CONSTRAINT FK_6EEAA67DF77D927C');
        $this->addSql('ALTER TABLE choix DROP CONSTRAINT FK_4F488091F347EFB');
        $this->addSql('ALTER TABLE commande DROP CONSTRAINT FK_6EEAA67DF347EFB');
        $this->addSql('ALTER TABLE api_token DROP CONSTRAINT FK_7BA2F5EBFB88E14F');
        $this->addSql('ALTER TABLE panier DROP CONSTRAINT FK_24CC0DF276C50E4A');
        $this->addSql('ALTER TABLE reset_password_request DROP CONSTRAINT FK_7CE748AA76ED395');
        $this->addSql('DROP SEQUENCE api_token_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE categorie_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE choix_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE commande_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE panier_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE produit_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE reset_password_request_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE stripe_payment_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE utilisateur_id_seq CASCADE');
        $this->addSql('DROP TABLE api_token');
        $this->addSql('DROP TABLE categorie');
        $this->addSql('DROP TABLE choix');
        $this->addSql('DROP TABLE commande');
        $this->addSql('DROP TABLE panier');
        $this->addSql('DROP TABLE produit');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE stripe_payment');
        $this->addSql('DROP TABLE utilisateur');
    }
}
