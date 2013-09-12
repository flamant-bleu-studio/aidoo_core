#!/usr/bin/perl
use strict;
use warnings;
use File::Copy::Recursive qw(fcopy rcopy dircopy fmove rmove dirmove);

#Check sur le nombre d'arguments (1 minimum)
($#ARGV>=0) || die "\nInsufficient number of parameters\nSyntax is: name_new_module [string] \n\n";

if ($ARGV[0] eq "help")
{
	print "\n";
	print "-------------------\n";
	print "- CreateModule.pl -\n";
	print "-------------------\n";
	print "This script take at least 1 arguments (name of the new module)\n\n";
}
else 
{
	createModule();
}


sub GetFilesList
{
        my $Path = $_[0];
        my $FileFound;
        my @FilesList=();

        # Lecture de la liste des fichiers
        opendir (my $FhRep, $Path)
                or die "Impossible d'ouvrir le repertoire $Path\n";
        my @Contenu = grep { !/^\.\.?$/ } readdir($FhRep);
        closedir ($FhRep);

        foreach my $FileFound (@Contenu) {
                # Traitement des fichiers
                if ( -f "$Path/$FileFound") {
                        push ( @FilesList, "$Path/$FileFound" );
                }
                # Traitement des repertoires
                elsif ( -d "$Path/$FileFound") {
                        # Boucle pour lancer la recherche en mode recursif
                        push (@FilesList, GetFilesList("$Path/$FileFound") );
                }

        }
        return @FilesList;
}

sub createModule
{
	#Variables
	my $username = getpwuid( $< );
	my $module_name = lc($ARGV[0]);
	my $module_name_up = ucfirst($module_name);
	my $repertoire1 = "../resources/MODULE_EXEMPLE";
	my $repertoire2 = "../application/modules/$module_name";

	#On écrase jamais un dossier
	if (-e $repertoire2){
		print "\nWarning : You can't force the suppression of \"$module_name\"\n";				
		print "You must delete the module \"$module_name\" before launch this script.\n";
		print "Script stopped.\n\n";
		exit
	} 
	
	#Copie du dossier
	dircopy($repertoire1,$repertoire2) or die("Impossible de copier $repertoire1 $!");

	#Boucle sur tout les fichiers et sous fichiers
	my @Files = GetFilesList ("../application/modules/$module_name");
	foreach my $File  (@Files) {
		open(FILE_IN,$File);
		my @contenu = <FILE_IN>;
		close(FILE_IN);
		 
		open(FILE_OUT,">$File");
		foreach my $ligne (@contenu)
		{
			$ligne =~ s/{exemple}/$module_name/g;
			chomp $ligne;
			$ligne =~ s/{Exemple}/$module_name_up/g;
			chomp $ligne;
			$ligne =~ s/exempleXML/$module_name/g;
			chomp $ligne;
			$ligne =~ s/{author}/$username/g;
			chomp $ligne;
			print FILE_OUT "$ligne\n";
		}
		close(FILE_OUT);

		if ("$File" =~ /Exemple/){
			my $file_new_name = $File;
			$file_new_name =~ s/Exemple/$module_name_up/g;
			rename $File, $file_new_name;
			#print "----- rename : ";
		}
		#print "$File\n";
	}
	print "--> Module $module_name has been created\n";
}
