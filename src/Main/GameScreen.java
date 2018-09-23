package Main;

import java.awt.Color;
import java.awt.event.KeyEvent;
import java.awt.event.MouseEvent;


public class GameScreen implements Screen {
	
	@Override
	public void display(GamePanel gamePanel) {
		gamePanel.setBackground(Color.white);
	}
	@Override
	public Screen update(KeyEvent key, MouseEvent mouse) {
		char keyChar = (key == null)?0:key.getKeyChar();

		if(keyChar == 'd') {
			System.out.println("CCOUT");
			return new StartScreen();
		}
		else 
			return this;
	}
}
