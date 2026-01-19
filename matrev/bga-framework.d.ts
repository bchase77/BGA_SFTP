declare namespace BGAFramework {
  interface Game {
    gamedatas: any;
    setup(gamedatas: any): void;
    onEnteringState(stateName: string, args: any): void;
    onLeavingState(stateName: string): void;
    onUpdateActionButtons(stateName: string, args: any): void;
    ajaxcall(url: string, args: any, handler: (response: any) => void): void;
    notifqueue: any[];
  }

  interface Player {
    id: number;
    name: string;
    color: string;
  }

  interface GameData {
    players: { [playerId: number]: Player };
    gamestate: {
      id: number;
      name: string;
      description: string;
    };
    // ... other common structures
  }
}
