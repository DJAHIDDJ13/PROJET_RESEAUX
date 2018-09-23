package Main;

import java.awt.Color;
import java.awt.event.KeyEvent;
import java.awt.event.MouseEvent;

public class StartScreen implements Screen {
	
	@Override
	public void display(GamePanel gamePanel) {
		gamePanel.setBackground(Color.black);
	}

	@Override
	public Screen update(KeyEvent key, MouseEvent mouse) {
		char keyChar = (key == null)?0:key.getKeyChar();
		if(keyChar == 'c') {
			System.out.println("CCIN");
			return new GameScreen();
		}
		else 
			return this;
	}
}
