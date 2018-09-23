package Main;

import java.awt.event.KeyEvent;
import java.awt.event.KeyListener;
import java.awt.event.MouseEvent;
import java.awt.event.MouseMotionListener;

import javax.swing.JFrame;

public class MainApplication extends JFrame implements MouseMotionListener, KeyListener {
	private static final long serialVersionUID = 6199383547986444977L;
	
	Screen screen;
	boolean running = true;
	KeyEvent key;
	MouseEvent mouse;
	GamePanel panel;
	
	public MainApplication() {
		super("SURVIVAL");

		panel = new GamePanel();
		screen = new StartScreen();

		setFocusable(true);
		setExtendedState(JFrame.MAXIMIZED_BOTH);
		setResizable(false);

		addKeyListener(this);
		addMouseMotionListener(this);
		add(panel);
		
		repaintImmediately();
		pack();
}
	public void update() {
		screen = screen.update(key, mouse);
	}
	public void repaintImmediately() {
		screen.display(panel);
	}
	public void run() {
		long lastLoopTime = System.nanoTime();
		final int TARGET_FPS = 60;
		final double OPTIMAL_TIME = 1000000000 / ((double) TARGET_FPS);
		long lastFpsTime = 0;
		while(running) {
			long now = System.nanoTime();
			long updateLength = now - lastLoopTime;
			lastLoopTime = now;
			double delta = updateLength / ((double) OPTIMAL_TIME);
			lastFpsTime += updateLength;
			if(lastFpsTime >= 1000000000) {
				lastFpsTime = 0;
			}
			if(delta < 1.0) {
				this.update();
			}
			this.repaintImmediately();
		}
		
	}
	public static void main(String[] args) {
		MainApplication app = new MainApplication();
		app.setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
		app.setVisible(true);
		app.run();
	}
	@Override
	public void keyPressed(KeyEvent e) {
		System.out.println("PRESSESD");
		key = e;
	}
	@Override
	public void keyReleased(KeyEvent arg0) {
		key = null;
	}
	@Override
	public void keyTyped(KeyEvent arg0) {}
	@Override
	public void mouseDragged(MouseEvent arg0) {}
	@Override
	public void mouseMoved(MouseEvent e) {
		mouse = e;		
	}
}
