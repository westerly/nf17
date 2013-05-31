
DROP TRIGGER IF EXISTS handle_create_capteurs;				   

CREATE OR REPLACE FUNCTION process_insert_capteur() RETURNS TRIGGER AS $insert_capteur$
    BEGIN
		-- Nouveau capteur dans un lieu => insertion d'un nouvel historique pour ce capteur
	  IF NEW.lieu_id is not null THEN
	  
		INSERT INTO historiques 
		VALUES(NEW.capteur_id,NOW(),NEW.lieu_id,null);
	  
	  END IF;
	  
      RETURN NULL; -- result is ignored since this is an AFTER trigger
    END;
$insert_capteur$ LANGUAGE plpgsql;


CREATE TRIGGER handle_create_capteurs
AFTER INSERT ON capteurs
    FOR EACH ROW EXECUTE PROCEDURE process_insert_capteur();
	
	
DROP TRIGGER IF EXISTS handle_update_capteurs;				   

CREATE OR REPLACE FUNCTION process_update_capteur() RETURNS TRIGGER AS $insert_capteur$
    BEGIN
		IF OLD.lieu_id is not null OR NEW.lieu_id is not null OR OLD.lieu_id <> NEW.lieu_id THEN
			-- Affectation d'un capteur dans un autre lieu à partir du lieu courant
		  IF NEW.lieu_id is not null THEN
			
			IF OLD.lieu_id is not null THEN 
				-- MAJ de la date de fin de l'historique pour le lieu courant
				UPDATE historiques SET fin = NOW() WHERE capteur_id = OLD.capteur_id AND lieu_id = OLD.lieu_id;
			END IF;
			
			-- insertion d'un nouvel historique pour l'affectation du capteur au nouveau lieu
			INSERT INTO historiques
			VALUES(OLD.capteur_id,NOW(),NEW.lieu_id,null);
			
		  ELSE -- Cas ou on met le capteur en réparation
			UPDATE historiques SET fin = NOW() WHERE capteur_id = OLD.capteur_id AND lieu_id = OLD.lieu_id;
		  END IF;
		END IF;
      RETURN NULL; -- result is ignored since this is an AFTER trigger
    END;
$insert_capteur$ LANGUAGE plpgsql;


CREATE TRIGGER handle_update_capteurs
AFTER UPDATE ON capteurs
    FOR EACH ROW EXECUTE PROCEDURE process_update_capteur();
	
