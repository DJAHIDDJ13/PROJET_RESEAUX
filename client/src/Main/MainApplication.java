package Main;

import java.awt.event.KeyEvent;
import java.awt.event.KeyListener;
import java.awt.event.MouseEvent;
import java.awt.event.MouseListener;

import javax.swing.JFrame;

public class MainApplication extends JFrame implements MouseListener, KeyListener {
	private static final long serialVersionUID = 6199383547986444977L;
	
	Screen screen;
	boolean running = true;
	KeyEvent key;
	MouseEvent mouse;
	GamePanel panel;
	
	public MainApplication() {
		super("Application sorties");

		panel = new GamePanel();
		screen = new StartScreen();
		setSize(500, 500);
		
		addKeyListener(this);
		addMouseListener(this);
		add(panel);
		
		repaintImmediately();
		pack();
	}

	public void repaintImmediately() {
		screen.display(panel);
	}

	@Override
	public void keyPressed(KeyEvent e) {
		System.out.println("PRESSESD");
		key = e;
		screen.respondToEvent(key, mouse);
		repaintImmediately();
	}
	@Override
	public void keyReleased(KeyEvent arg0) {
		key = null;
	}
	@Override
	public void keyTyped(KeyEvent arg0) {}



	@Override
	public void mouseClicked(MouseEvent e) {
		mouse = e;
		screen.respondToEvent(key, mouse);
		repaintImmediately();
	}

	@Override
	public void mouseEntered(MouseEvent arg0) {}

	@Override
	public void mouseExited(MouseEvent arg0) {}

	@Override
	public void mousePressed(MouseEvent arg0) {}
	
	@Override
	public void mouseReleased(MouseEvent arg0) {}
	
	public static void main(String[] args) {
		MainApplication app = new MainApplication();
		app.setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
		app.setVisible(true);
	}
}
