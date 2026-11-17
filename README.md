# SpaceShooter_beta_2.1

Bản tái cấu trúc bằng kế thừa + đa hình (từ 2.0), cộng thêm bất tử sau khi mất máu và cơ chế sinh quái mới (2.1).

Hành vi game giữ nguyên như bản cũ; chỉ thay đổi cách tổ chức code.
File save cũ (`savegame_csharp.txt`) vẫn dùng được.

## Cấu trúc

```
GameCore/                      (C++ -> GameCore.dll)
  Common.h                     DllExport, enum Difficulty, EnemyType
  Entity.h                     lớp cha: x, y, Width(), Height(), Overlaps()
  Projectiles.h/.cpp           Bullet -> PlayerBullet, EnemyBullet ; Missile
  Ability.h/.cpp               Ability -> EmpAbility, MissileAbility
  Player.h/.cpp                người chơi, Upgrades
  Enemy.h/.cpp                 Enemy (trừu tượng): khung Update() dùng chung
  NormalEnemies.h/.cpp         NormalEnemy -> Grunt, Gunner, GoldDrone, Rammer, Spreader, ShieldGunner
  Bosses.h/.cpp                Boss -> FanBoss -> RedBoss, CyanBoss ; Boss -> OrangeBoss
  EnemyFactory.h/.cpp          EnemyType -> đối tượng Enemy đúng lớp
  Level.h/.cpp                 Level -> NormalLevel, SpecialLevel -> AmbushLevel, EndlessLevel (chưa gắn), BossLevel (trừu tượng) -> Red/Cyan/Twin/OrangeBossLevel
  World.h/.cpp                 trạng thái ván chơi + vòng lặp Update()
  Shop.h/.cpp                  các hàm mua nâng cấp
  SaveSystem.h/.cpp            ghi/đọc file save (định dạng cũ)
  GameApi.cpp                  các hàm DllExport (cầu nối sang C#)

SpaceShooterCSharp/            (C# WinForms)
  Program.cs, GameForm.cs      cửa sổ + timer, giữ 1 GameScreen hiện tại
  Difficulty.cs, LevelKind.cs, GameInfo.cs (tên + phiên bản game)
  Native/GameCore.cs           toàn bộ [DllImport]
  UI/                          UiStyle (font, vẽ nút/chữ), UiButton
  Rendering/EnemySprite.cs     EnemySprite -> OrangeBossSprite
  Screens/                     GameScreen (trừu tượng) -> MenuScreen, LevelSelectScreen, PlayScreen,
                               EndScreen -> GameOverScreen, VictoryScreen
  Screens/Upgrades/            UpgradeMainScreen ; UpgradeScreen -> AtkUpgradeScreen, AbilityUpgradeScreen,
                               UpgradeTreeScreen -> DefUpgradeScreen, HpUpgradeScreen ; UpgradeNode
```

## Đa hình thay thế cái gì?

| Trước | Sau |
|---|---|
| `if (type == 1) ... else if (type == 8) ...` rải khắp (HP, sát thương, bắn, thưởng, kích thước, màu) | Mỗi loại quái là một lớp, override hàm ảo: `Fire()`, `BaseHp()`, `ShootInterval()`, ... |
| `if (e.type == 7 && e.phase == 1)` lặp 3 lần | `OrangeBoss::OnHpDepleted()` |
| `level == 5 \|\| level == 10 \|\| ...` và mẫu "cứ 5 màn là boss" ở cả C++ lẫn C# | `Level` + lớp con, chọn trong `Level::Create()`; C# hỏi `IsBossLevel()`, `IsLevelCompleted()`, `GetLevelKind()` |
| `enum GameState` + chuỗi if/else trong OnPaint, MouseClick, KeyDown | `GameScreen` + lớp con: `Draw`, `OnMouseClick`, `OnKeyDown`, `Update` |
| Biến toàn cục trong GameCore.cpp | `World` chứa trạng thái, đối tượng nhận `World&` |

Cố ý KHÔNG dùng kế thừa cho `Shop`: các nâng cấp chỉ khác nhau về số liệu (giá, cấp tối đa), không khác về hành vi.
Ở C#, các nâng cấp được khai báo dưới dạng dữ liệu bằng `UpgradeNode`.

## Thêm một loại quái mới

1. Thêm giá trị vào `EnemyType` (Common.h) và một dòng vào `EnemySprite.Sprites` (C#).
2. Tạo lớp kế thừa `NormalEnemy` (hoặc `Boss`), khai báo số liệu, override `Fire()` nếu có bắn.
3. Thêm một dòng `case` vào `CreateEnemy()` (EnemyFactory.cpp).

## Thêm vào Visual Studio

- Project C++ (Dynamic Library, tên `GameCore`): thêm TẤT CẢ file .h/.cpp trong thư mục `GameCore`,
  xoá `GameCore.cpp` cũ. Nếu dùng template DLL: C/C++ -> Precompiled Headers -> Not Using Precompiled Headers.
  Chuẩn ngôn ngữ cần C++14 trở lên (mặc định của VS đã đủ).
- Project C#: xoá `Program.cs` cũ, thêm toàn bộ file trong `SpaceShooterCSharp` (giữ nguyên thư mục con).
  Tên namespace của mọi file là `SpaceShooterCSharp`.
- Các bước x64, post-build copy `GameCore.dll` làm như đã hướng dẫn trước.

## Các thay đổi nhỏ có chủ đích

- Hàm DllImport trả về `bool` có thêm `[return: MarshalAs(UnmanagedType.U1)]` (bool của C++ chỉ 1 byte).
- Thêm 2 hàm xuất: `IsBossLevel()`, `IsLevelCompleted()` để C# không phải lặp lại luật màn chơi.
- `GetEnemyX(i)`, `GetBulletX(i)`, ... trả về 0 nếu `i` ngoài phạm vi (trước đây truy cập sai bộ nhớ).
- Màn nâng cấp ghi file save sau mỗi lần bấm nút mua (trước đây một số nhánh còn ghi cả khi bấm vào chỗ trống).

## Thêm màn đặc biệt / đổi màn boss

Màn boss cũng là một loại màn đặc biệt (`BossLevel` kế thừa `SpecialLevel`). Không còn quy luật "cứ 5 màn là boss":
màn nào thuộc loại nào chỉ do danh sách `case` trong `Level::Create()` (Level.cpp) quyết định.

- Dời boss sang màn khác: đổi số trong `Level::Create()`. Nút ở màn chọn màn tự đổi theo (C# lấy loại màn qua `GetLevelKind()`).
- Thêm màn đặc biệt mới: tạo lớp kế thừa `SpecialLevel`, override phần khác biệt (`TargetScore()`, `RollEnemyType()`, `SpawnChance()`, `IsCompleted()`...), thêm `case` vào `Level::Create()`.
  Nếu muốn nút có chữ/màu riêng: thêm giá trị vào `LevelKind` (Common.h và LevelKind.cs) và một `case` trong `RefreshLevelButtons()`.
- Thêm màn boss mới: kế thừa `BossLevel` và viết `SpawnBosses()`.

## Chương 3 là placeholder

Màn chọn màn vẫn hiện 10 nút mỗi chương. Từ màn 25 trở đi nút màu xám và không bấm được
(hằng `LastPlayableLevel = 24` trong `LevelSelectScreen.cs`). Khi làm thêm màn, chỉ cần tăng hằng số này.

## Thay đổi trong 2.1

**1. Bất tử 1 giây sau khi mất máu** (`Player::TakeDamage`)
- Khi người chơi bị trừ MÁU trực tiếp, họ được bất tử 60 khung hình (1 giây): mọi sát thương trong lúc đó bị bỏ qua.
- Khiên chặn hết sát thương thì không kích hoạt bất tử (và các đòn liên tiếp vẫn trừ khiên bình thường).
- Đòn làm vỡ khiên và tràn sang máu (mất máu thật) thì có bất tử.
- Lúc bất tử, nhân vật nhấp nháy giữa đặc và mờ (`PlayScreen.DrawPlayer`). Đạn địch trúng lúc này vẫn biến mất, quái thường đâm vào vẫn tự nổ.
- Thời gian bất tử không lưu vào file save (nạp save thì bắt đầu từ 0).
- Đổi thời lượng: hằng `Player::InvincibleFrames` trong Player.h.

**2. Cơ chế sinh quái mới** (`Level::SpawnEnemies`)
- Cũ: mỗi khung hình có (2 + số màn)% xác suất sinh quái, nên tốc độ sinh tăng không ngừng (màn 24 tới ~15 quái/giây).
- Mới: sinh theo nhịp đều tính bằng quái/giây: 1,8 ở màn 1, tăng 0,2 mỗi màn, dừng ở tối đa 6,0.
  Màn boss luôn 1,8 quái/giây (như cũ). Vị trí và loại quái vẫn ngẫu nhiên theo bảng cũ.
- Giới hạn 100 quái thường trên sân (boss không tính). Sân đầy thì ngừng sinh; có chỗ trống là sinh tiếp.
- Các con số cân bằng nằm hết trong Level.cpp (`kBaseSpawnRate`, `kSpawnRateGrowth`, `kMaxSpawnRate`);
  giới hạn là hàm ảo `Level::MaxEnemiesOnField()` (đổi giá trị hoặc override ở lớp con; 0 = không giới hạn).

| Màn | 1 | 3 | 7 | 13 | 24 |
|---|---|---|---|---|---|
| Cũ (quái/giây) | 1,8 | 3,0 | 5,4 | 9,0 | 15,6 |
| Mới (quái/giây) | 1,8 | 2,2 | 3,0 | 4,2 | 6,0 |

**3. Endless (chưa có trong game)**: lớp `EndlessLevel` giữ nguyên cơ chế sinh quái cũ, không giới hạn quái và không bao giờ thắng.
Lớp này CHƯA được gắn vào `Level::Create()` vì game chưa có chế độ endless. Khi làm chế độ này, bạn còn cần quyết định
cách hiển thị điểm trên HUD (hiện HUD in "SCORE: x / TargetScore").

**4. Đổi tên** thành SpaceShooter_beta_2.1: tiêu đề cửa sổ lấy từ `GameInfo.Name`. Tên namespace `SpaceShooterCSharp` giữ nguyên.

## Khác biệt về luật chơi so với code gốc (tích luỹ)

- Màn 13 (AMBUSH) trước đây không thể thắng; giờ thắng khi đạt 3000 điểm như màn thường (`AmbushLevel` kế thừa `SpecialLevel`).
- Màn 25 và 30 không còn là màn boss. Chúng là màn thường placeholder, và nút bị khoá ở màn chọn màn.
- Bất tử 1 giây sau khi mất máu, và cơ chế sinh quái mới có giới hạn 100 (mục 1 và 2 ở trên).

## Giữ nguyên theo ý bạn

- Nhiều viên đạn trúng cùng một quái trong cùng một khung hình vẫn có thể thưởng điểm/vàng nhiều lần (comment trong `Enemy.cpp` chỉ cách sửa nếu sau này cần).
- Độ khó Hard cộng thẳng 1 sát thương cho quái thường.
- Boss gây 10 sát thương mỗi lần chạm (giờ chỉ tính 1 lần mỗi giây nhờ bất tử).
- Bố cục màn nâng cấp (dòng GOLD đè tiêu đề, đường nối cắt chữ mô tả) giữ như cũ.
