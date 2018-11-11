package Main;

import java.awt.event.KeyEvent;
import java.awt.event.MouseEvent;

public interface Screen {
	public void display(GamePanel gamePanel);
	public Screen respondToEvent(KeyEvent key, MouseEvent mouse);
}